<?php

namespace App\Http\Controllers;

use App\Mail\PagoAprobado;
use App\Mail\PagoRechazado;
use App\Models\Aspirante;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class PagoController extends Controller
{
    // ─── Público: vista de pago ───────────────────────────────────────────────

    public function create(Request $request)
    {
        $folio = strtoupper($request->query('folio', ''));

        if (!$folio) {
            return redirect()->route('aspirantes.seguimiento')
                ->withErrors(['folio' => 'Debes consultar tu folio primero.']);
        }

        $aspirante = Aspirante::with('programa')
            ->where('folio', $folio)
            ->where('estado', 'aprobado')
            ->first();

        if (!$aspirante) {
            return redirect()->route('aspirantes.seguimiento')
                ->withErrors(['folio' => 'Folio no encontrado o solicitud aún no aprobada.']);
        }

        // Si ya tiene un pago aprobado, redirigir a confirmación
        if (Pago::where('aspirante_id', $aspirante->id)->where('estado', 'aprobado')->exists()) {
            return redirect()->route('aspirantes.pago.confirmacion', ['status' => 'aprobado']);
        }

        // Reutilizar preference de sesión para no crear una nueva en cada visita
        $sessionKey   = 'mp_preference_' . $folio;
        $preferenceId = session($sessionKey);

        if (!$preferenceId) {
            try {
                $this->configurarMP();

                $preferenceData = [
                    'items' => [[
                        'title'       => 'Inscripción UICM — ' . ($aspirante->programa->nombre ?? 'Programa académico'),
                        'quantity'    => 1,
                        'unit_price'  => 3500.00,
                        'currency_id' => 'MXN',
                    ]],
                    'payer' => [
                        'name'    => $aspirante->nombre,
                        'surname' => trim($aspirante->apellido_paterno . ' ' . $aspirante->apellido_materno),
                        'email'   => $aspirante->email,
                    ],
                    'external_reference'   => $aspirante->folio,
                    'statement_descriptor' => 'UICM Inscripcion',
                ];

                // back_urls y notification_url solo en producción (MP rechaza localhost)
                if (!app()->environment('local')) {
                    $preferenceData['back_urls'] = [
                        'success' => route('aspirantes.pago.retorno'),
                        'failure' => route('aspirantes.pago.retorno'),
                        'pending' => route('aspirantes.pago.retorno'),
                    ];
                    $preferenceData['notification_url'] = route('mp.webhook');
                }

                $preference   = (new PreferenceClient())->create($preferenceData);
                $preferenceId = $preference->id;

                session([$sessionKey => $preferenceId]);

            } catch (\MercadoPago\Exceptions\MPApiException $e) {
                Log::error('MP preference error: ' . $e->getMessage(), [
                    'response' => $e->getApiResponse()?->getContent(),
                ]);
                return back()->withErrors(['pago' => 'Error al conectar con Mercado Pago: ' . $e->getMessage()]);
            }
        }

        return view('aspirantes.pago', [
            'aspirante'    => $aspirante,
            'preferenceId' => $preferenceId,
            'publicKey'    => config('services.mercadopago.public_key'),
        ]);
    }

    // ─── Público: procesar pago desde el brick ────────────────────────────────

    public function procesar(Request $request)
    {
        $data  = $request->json()->all();
        $folio = strtoupper($data['folio'] ?? '');

        $aspirante = Aspirante::with('programa')->where('folio', $folio)->first();

        if (!$aspirante) {
            return response()->json(['status' => 'error', 'detail' => 'Folio no válido.'], 422);
        }

        try {
            $this->configurarMP();

            $paymentData = [
                'transaction_amount' => 3500.00,
                'description'        => 'Inscripción UICM',
                'payment_method_id'  => $data['payment_method_id'],
                'payer'              => ['email' => $data['payer']['email'] ?? ''],
            ];

            // Datos adicionales solo para pagos con tarjeta
            if (!empty($data['token'])) {
                $paymentData['token']        = $data['token'];
                $paymentData['installments'] = (int) ($data['installments'] ?? 1);
                $paymentData['issuer_id']    = $data['issuer_id'] ?? null;
            }

            $client  = new PaymentClient();
            $payment = $client->create($paymentData);

            $estado       = $this->mapearEstado($payment->status);
            $preferenceId = session('mp_preference_' . $folio);

            // Solo crear el Pago si fue aprobado o está pendiente (ej. OXXO)
            if (in_array($estado, ['aprobado', 'pendiente'])) {
                $pago = Pago::create([
                    'aspirante_id'     => $aspirante->id,
                    'concepto'         => 'inscripcion',
                    'periodo'          => date('Y') . '-1',
                    'monto'            => 3500.00,
                    'fecha_pago'       => now()->toDateString(),
                    'estado'           => $estado,
                    'mp_preference_id' => $preferenceId,
                    'mp_payment_id'    => $payment->id,
                ]);

                if ($estado === 'aprobado') {
                    Mail::to($aspirante->email)->send(new PagoAprobado($pago));
                }
            }

            $redirectUrl = match($estado) {
                'aprobado'  => route('aspirantes.pago.confirmacion', ['status' => 'aprobado']),
                'pendiente' => route('aspirantes.pago.confirmacion', ['status' => 'pendiente']),
                default     => null,
            };

            return response()->json([
                'status'       => $payment->status,
                'detail'       => $payment->status_detail,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('MP procesar error: ' . $e->getMessage(), [
                'response' => $e->getApiResponse()?->getContent(),
                'status'   => $e->getApiResponse()?->getStatusCode(),
            ]);
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('MP procesar error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ─── Público: retorno desde back_url ─────────────────────────────────────

    public function retorno(Request $request)
    {
        $status = $request->query('payment_status', $request->query('status', 'pendiente'));

        $estadoMapa = [
            'approved' => 'aprobado',
            'pending'  => 'pendiente',
            'failure'  => 'rechazado',
            'rejected' => 'rechazado',
        ];

        $estado = $estadoMapa[$status] ?? 'pendiente';

        return redirect()->route('aspirantes.pago.confirmacion', ['status' => $estado]);
    }

    // ─── Webhook IPN de Mercado Pago ──────────────────────────────────────────

    public function webhook(Request $request)
    {
        $type = $request->query('type') ?? $request->query('topic');
        $id   = $request->query('data_id')
            ?? $request->input('data.id')
            ?? $request->query('id');

        if ($type !== 'payment' || !$id) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            $this->configurarMP();

            $client  = new PaymentClient();
            $payment = $client->get((int) $id);
            $estado  = $this->mapearEstado($payment->status);

            // Buscar el pago por mp_payment_id o por folio (external_reference)
            $pago = Pago::where('mp_payment_id', $payment->id)->first();

            if (!$pago && $payment->external_reference) {
                $aspirante = Aspirante::where('folio', $payment->external_reference)->first();
                if ($aspirante) {
                    $pago = Pago::where('aspirante_id', $aspirante->id)
                        ->where('estado', 'pendiente')
                        ->whereNotNull('mp_preference_id')
                        ->latest()
                        ->first();
                }
            }

            if ($pago) {
                $estadoAnterior = $pago->estado;

                $pago->update([
                    'mp_payment_id' => $payment->id,
                    'estado'        => $estado,
                ]);

                if ($estado === 'aprobado' && $estadoAnterior !== 'aprobado') {
                    Mail::to($pago->aspirante->email)->send(new PagoAprobado($pago));
                }
            }
        } catch (\Exception $e) {
            Log::error('MP Webhook error: ' . $e->getMessage());
        }

        return response()->json(['status' => 'ok']);
    }

    // ─── Público: página de confirmación ─────────────────────────────────────

    public function confirmacion(Request $request)
    {
        $status = $request->query('status', 'pendiente');
        return view('aspirantes.pago_confirmacion', compact('status'));
    }

    // ─── Finanzas: listado y detalle ──────────────────────────────────────────

    public function index()
    {
        $pagos = Pago::with('aspirante.programa')->latest()->get();
        return view('finanzas.pagos.index', compact('pagos'));
    }

    public function show(Pago $pago)
    {
        $pago->load('aspirante.programa');

        $mpPago = null;

        if ($pago->mp_payment_id) {
            try {
                $this->configurarMP();
                $mpPago = (new PaymentClient())->get((int) $pago->mp_payment_id);
            } catch (\Exception $e) {
                Log::error('MP show error: ' . $e->getMessage());
            }
        }

        return view('finanzas.pagos.show', compact('pago', 'mpPago'));
    }

    // ─── Finanzas: aprobar/rechazar manual ────────────────────────────────────

    public function aprobar(Pago $pago)
    {
        if ($pago->estado !== 'pendiente') {
            abort(403, 'Este pago ya fue procesado.');
        }

        $pago->update(['estado' => 'aprobado']);

        Mail::to($pago->aspirante->email)->send(new PagoAprobado($pago));

        return redirect()->back()->with('success', 'Pago aprobado correctamente.');
    }

    public function rechazar(Request $request, Pago $pago)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        if ($pago->estado !== 'pendiente') {
            abort(403, 'Este pago ya fue procesado.');
        }

        $pago->update([
            'estado'        => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        Mail::to($pago->aspirante->email)->send(new PagoRechazado($pago));

        return redirect()->back()->with('success', 'Pago rechazado correctamente.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function configurarMP(): void
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    private function mapearEstado(string $mpStatus): string
    {
        return match($mpStatus) {
            'approved'                      => 'aprobado',
            'pending', 'in_process',
            'authorized', 'in_mediation'    => 'pendiente',
            default                         => 'rechazado',
        };
    }
}
