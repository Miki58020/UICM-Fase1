<?php

namespace App\Http\Controllers;

use App\Mail\PagoAprobado;
use App\Mail\PagoRechazado;
use App\Models\Aspirante;
use App\Models\ConfiguracionMercadopago;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MercadoPago\Client\Common\RequestOptions;
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

        if (Pago::where('aspirante_id', $aspirante->id)->where('estado', 'aprobado')->exists()) {
            return redirect()->route('aspirantes.pago.confirmacion', ['status' => 'aprobado']);
        }

        $monto          = $this->calcularMonto($aspirante->programa);
        $sessionKeyPref = 'mp_preference_' . $folio . '_' . (int) $monto;
        $sessionKeyUrl  = 'mp_checkout_url_' . $folio . '_' . (int) $monto;
        $preferenceId   = session($sessionKeyPref);
        $checkoutUrl    = session($sessionKeyUrl);

        if (!$preferenceId || !$checkoutUrl) {
            try {
                $this->configurarMP();

                $preferenceData = [
                    'items' => [[
                        'title'       => 'Inscripcion UICM — ' . ($aspirante->programa->nombre ?? 'Programa academico'),
                        'quantity'    => 1,
                        'unit_price'  => $monto,
                        'currency_id' => 'MXN',
                    ]],
                    'external_reference'   => $aspirante->folio,
                    'statement_descriptor' => 'UICM Inscripcion',
                ];

                $mpConfig = ConfiguracionMercadopago::activa();
                if ($mpConfig) {
                    $preferenceData['back_urls'] = [
                        'success' => $mpConfig->back_url_success,
                        'failure' => $mpConfig->back_url_failure,
                        'pending' => $mpConfig->back_url_pending,
                    ];
                    $preferenceData['auto_return'] = 'approved';
                    if ($mpConfig->notification_url) {
                        $preferenceData['notification_url'] = $mpConfig->notification_url;
                    }
                }

                $preference   = (new PreferenceClient())->create($preferenceData);
                $preferenceId = $preference->id;
                $checkoutUrl  = app()->environment('production')
                    ? $preference->init_point
                    : $preference->sandbox_init_point;

                session([
                    $sessionKeyPref => $preferenceId,
                    $sessionKeyUrl  => $checkoutUrl,
                ]);

            } catch (\MercadoPago\Exceptions\MPApiException $e) {
                Log::error('MP preference MPApiException', [
                    'message'  => $e->getMessage(),
                    'httpCode' => $e->getApiResponse()?->getStatusCode(),
                    'body'     => $e->getApiResponse()?->getContent(),
                ]);
            } catch (\Exception $e) {
                Log::error('MP preference error: ' . $e->getMessage());
            }
        }

        return view('aspirantes.pago', [
            'aspirante'   => $aspirante,
            'checkoutUrl' => $checkoutUrl,
            'monto'       => $monto,
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

            $monto = $this->calcularMonto($aspirante->programa);

            $paymentData = [
                'transaction_amount' => (float) $monto,
                'description'        => 'Inscripcion UICM',
                'payment_method_id'  => (string) ($data['payment_method_id'] ?? ''),
                'payer'              => ['email' => (string) ($data['payer']['email'] ?? '')],
            ];

            // Datos adicionales solo para pagos con tarjeta
            if (!empty($data['token'])) {
                $paymentData['token']        = $data['token'];
                $paymentData['installments'] = (int) ($data['installments'] ?? 1);
                if (!empty($data['issuer_id'])) {
                    $paymentData['issuer_id'] = (int) $data['issuer_id'];
                }
            }

            Log::info('MP payment data', $paymentData);

            $requestOptions = new RequestOptions();
            $requestOptions->setCustomHeaders(['X-Idempotency-Key: ' . (string) Str::uuid()]);

            $client  = new PaymentClient();
            $payment = $client->create($paymentData, $requestOptions);

            Log::info('MP payment response', ['status' => $payment->status, 'detail' => $payment->status_detail, 'id' => $payment->id]);

            $mpEstado     = $payment->status;
            $preferenceId = session('mp_preference_' . $folio);

            // Guardar el pago si MP lo procesó (aprobado o pendiente como OXXO)
            // El estado interno siempre queda 'pendiente' hasta que Finanzas lo valide manualmente
            if (in_array($mpEstado, ['approved', 'pending', 'in_process', 'authorized'])) {
                Pago::create([
                    'aspirante_id'     => $aspirante->id,
                    'concepto'         => 'inscripcion',
                    'periodo'          => date('Y') . '-1',
                    'monto'            => $monto,
                    'fecha_pago'       => now()->toDateString(),
                    'estado'           => 'pendiente',
                    'mp_preference_id' => $preferenceId,
                    'mp_payment_id'    => $payment->id,
                ]);
                // El correo de aprobación lo envía Finanzas al validar manualmente
            }

            $redirectUrl = in_array($mpEstado, ['approved', 'pending', 'in_process', 'authorized'])
                ? route('aspirantes.pago.confirmacion', ['status' => 'pendiente'])
                : null;

            return response()->json([
                'status'       => $payment->status,
                'detail'       => $payment->status_detail,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiContent = $e->getApiResponse()?->getContent();
            Log::error('MP procesar MPApiException', [
                'message'  => $e->getMessage(),
                'httpCode' => $e->getApiResponse()?->getStatusCode(),
                'body'     => $apiContent,
            ]);
            $detail = $e->getMessage();
            if (is_array($apiContent) && !empty($apiContent['cause'])) {
                $detail .= ' | cause: ' . json_encode($apiContent['cause']);
            }
            return response()->json(['status' => 'error', 'detail' => $detail], 500);
        } catch (\Exception $e) {
            Log::error('MP procesar error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ─── Público: retorno desde back_url ─────────────────────────────────────

    public function retorno(Request $request)
    {
        $status    = $request->query('payment_status', $request->query('status', 'pendiente'));
        $paymentId = $request->query('payment_id');
        $extRef    = $request->query('external_reference');

        // Crear el registro de pago cuando MP redirige de vuelta (Checkout Pro).
        // El webhook hace lo mismo en producción; firstOrCreate evita duplicados.
        if ($paymentId && $extRef) {
            try {
                $this->configurarMP();
                $aspirante = Aspirante::where('folio', strtoupper($extRef))->first();

                if ($aspirante) {
                    $payment = (new PaymentClient())->get((int) $paymentId);

                    if (in_array($payment->status, ['approved', 'pending', 'in_process', 'authorized'])) {
                        Pago::firstOrCreate(
                            ['mp_payment_id' => (string) $payment->id],
                            [
                                'aspirante_id'     => $aspirante->id,
                                'concepto'         => 'inscripcion',
                                'periodo'          => date('Y') . '-1',
                                'monto'            => $payment->transaction_amount,
                                'fecha_pago'       => now()->toDateString(),
                                'estado'           => 'pendiente',
                                'mp_preference_id' => $payment->preference_id ?? null,
                            ]
                        );
                    }
                }
            } catch (\Exception $e) {
                Log::error('MP retorno error: ' . $e->getMessage());
            }
        }

        $estadoMapa = [
            'approved' => 'aprobado',
            'pending'  => 'pendiente',
            'failure'  => 'rechazado',
            'rejected' => 'rechazado',
        ];

        return redirect()->route('aspirantes.pago.confirmacion', [
            'status' => $estadoMapa[$status] ?? 'pendiente',
        ]);
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

            $client    = new PaymentClient();
            $payment   = $client->get((int) $id);
            $aspirante = null;

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
                $pago->update(['mp_payment_id' => $payment->id]);
            } elseif ($aspirante && in_array($payment->status, ['approved', 'pending', 'in_process', 'authorized'])) {
                Pago::firstOrCreate(
                    ['mp_payment_id' => (string) $payment->id],
                    [
                        'aspirante_id'     => $aspirante->id,
                        'concepto'         => 'inscripcion',
                        'periodo'          => date('Y') . '-1',
                        'monto'            => $payment->transaction_amount,
                        'fecha_pago'       => now()->toDateString(),
                        'estado'           => 'pendiente',
                        'mp_preference_id' => $payment->preference_id ?? null,
                    ]
                );
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
        $pagos = Pago::with(['aspirante.programa', 'alumno.programa'])->latest()->get();
        return view('finanzas.pagos.index', compact('pagos'));
    }

    public function show(Pago $pago)
    {
        $pago->load(['aspirante.programa', 'alumno.programa']);

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

        $pago->update([
            'estado'     => 'aprobado',
            'fecha_pago' => $pago->fecha_pago ?? now()->toDateString(),
        ]);

        $email = $pago->aspirante?->email ?? $pago->alumno?->email;
        if ($email) {
            Mail::to($email)->send(new PagoAprobado($pago));
        }

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

        $email = $pago->aspirante?->email ?? $pago->alumno?->email;
        if ($email) {
            Mail::to($email)->send(new PagoRechazado($pago));
        }

        return redirect()->back()->with('success', 'Pago rechazado correctamente.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function configurarMP(): void
    {
        $config = ConfiguracionMercadopago::activa();
        if (!$config) {
            throw new \RuntimeException('No hay configuración activa de MercadoPago.');
        }
        MercadoPagoConfig::setAccessToken($config->access_token);
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

    private function calcularMonto(?\App\Models\Programa $programa): float
    {
        $nivel  = $programa?->nivel ?? 'licenciatura';
        $tarifa = \App\Models\TarifaInscripcion::where('nivel', $nivel)->first();

        return $tarifa ? (float) $tarifa->monto : match($nivel) {
            'maestria'  => 4000.00,
            'doctorado' => 5000.00,
            default     => 3000.00,
        };
    }
}
