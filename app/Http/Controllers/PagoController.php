<?php

namespace App\Http\Controllers;

use App\Mail\PagoAprobado;
use App\Mail\PagoRechazado;
use App\Models\Alumno;
use App\Models\Aspirante;
use App\Models\ConfiguracionMercadopago;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\TarifaInscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $tarifa         = $this->obtenerTarifaInscripcion($aspirante->programa);
        $monto          = $this->calcularMonto($aspirante->programa);
        $descuentoKey   = (int) ($tarifa?->descuento ?? 0);
        $sessionKeyPref = 'mp_preference_' . $folio . '_' . (int) $monto . '_' . $descuentoKey;
        $sessionKeyUrl  = 'mp_checkout_url_' . $folio . '_' . (int) $monto . '_' . $descuentoKey;
        $preferenceId   = session($sessionKeyPref);
        $checkoutUrl    = session($sessionKeyUrl);

        if (!$preferenceId || !$checkoutUrl) {
            try {
                $this->configurarMP();

                $nombrePrograma = $aspirante->programa->nombre ?? 'Programa academico';
                $tituloItem     = 'Inscripcion UICM — ' . $nombrePrograma;
                if ($tarifa && $tarifa->descuentoVigente()) {
                    $tituloItem .= ' (' . number_format($tarifa->descuento, 0) . '% dto.)';
                }

                $preferenceData = [
                    'items' => [[
                        'title'       => $tituloItem,
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
            'tarifa'      => $tarifa,
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

            $tarifa = $this->obtenerTarifaInscripcion($aspirante->programa);
            $monto  = $this->calcularMonto($aspirante->programa);

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
                    'descuento'        => $this->descuentoAplicado($tarifa),
                    'monto_original'   => $tarifa->monto ?? $monto,
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
                $aspirante = Aspirante::with('programa')->where('folio', strtoupper($extRef))->first();

                if ($aspirante) {
                    $payment = (new PaymentClient())->get((int) $paymentId);

                    if (in_array($payment->status, ['approved', 'pending', 'in_process', 'authorized'])) {
                        $tarifa = $this->obtenerTarifaInscripcion($aspirante->programa);

                        Pago::firstOrCreate(
                            ['mp_payment_id' => (string) $payment->id],
                            [
                                'aspirante_id'     => $aspirante->id,
                                'concepto'         => 'inscripcion',
                                'periodo'          => date('Y') . '-1',
                                'monto'            => $payment->transaction_amount,
                                'descuento'        => $this->descuentoAplicado($tarifa),
                                'monto_original'   => $tarifa->monto ?? $payment->transaction_amount,
                                'fecha_pago'       => now()->toDateString(),
                                'estado'           => 'pendiente',
                                'mp_preference_id' => $payment->preference_id ?? null,
                            ]
                        );
                    }
                }
            } catch (\MercadoPago\Exceptions\MPApiException $e) {
                Log::error('MP retorno MPApiException', [
                    'message'  => $e->getMessage(),
                    'httpCode' => $e->getApiResponse()?->getStatusCode(),
                    'body'     => $e->getApiResponse()?->getContent(),
                    'payment_id' => $paymentId,
                ]);
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

            // Pago de un alumno autenticado (colegiatura/cuatrimestre/reinscripcion)
            if (!$pago && str_starts_with((string) $payment->external_reference, 'PAGO-')) {
                $pagoId = (int) str_replace('PAGO-', '', $payment->external_reference);
                $pago   = Pago::find($pagoId);
            }

            if (!$pago && $payment->external_reference) {
                $aspirante = Aspirante::with('programa')->where('folio', $payment->external_reference)->first();
                if ($aspirante) {
                    $pago = Pago::where('aspirante_id', $aspirante->id)
                        ->where('estado', 'pendiente')
                        ->whereNotNull('mp_preference_id')
                        ->latest()
                        ->first();
                }
            }

            if ($pago) {
                $pago->update([
                    'mp_payment_id' => $payment->id,
                    'fecha_pago'    => $pago->fecha_pago ?? now()->toDateString(),
                ]);
            } elseif ($aspirante && in_array($payment->status, ['approved', 'pending', 'in_process', 'authorized'])) {
                $tarifa = $this->obtenerTarifaInscripcion($aspirante->programa);

                Pago::firstOrCreate(
                    ['mp_payment_id' => (string) $payment->id],
                    [
                        'aspirante_id'     => $aspirante->id,
                        'concepto'         => 'inscripcion',
                        'periodo'          => date('Y') . '-1',
                        'monto'            => $payment->transaction_amount,
                        'descuento'        => $this->descuentoAplicado($tarifa),
                        'monto_original'   => $tarifa->monto ?? $payment->transaction_amount,
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

    // ─── Alumno: pagar un cargo pendiente dentro del portal ───────────────────

    public function pagarAlumno(Pago $pago)
    {
        $alumno = Alumno::where('user_id', Auth::id())->with('programa')->firstOrFail();
        abort_if($pago->alumno_id !== $alumno->id, 403);
        abort_if($pago->estado !== 'pendiente', 403, 'Este pago ya fue procesado.');

        // Colegiatura y reinscripción tienen descuento vigente por fechas: el monto se
        // recalcula siempre con la fecha de hoy antes de generar el cobro en Mercado Pago.
        if ($pago->concepto === 'colegiatura') {
            $tarifa = TarifaInscripcion::where('nivel', $alumno->programa->nivel)
                ->where('tipo', 'colegiatura')
                ->first();

            if ($tarifa) {
                $pago->update([
                    'monto'          => $tarifa->precioParaFecha(now()),
                    'descuento'      => $tarifa->descuentoVigenteEnFecha(now()) ? $tarifa->descuento : 0,
                    'monto_original' => $tarifa->monto,
                ]);
            }
        } elseif ($pago->concepto === 'reinscripcion') {
            $tarifa = TarifaInscripcion::where('nivel', $alumno->programa->nivel)
                ->where('tipo', 'cuatrimestre')
                ->first();

            if ($tarifa) {
                $pago->update([
                    'monto'          => $tarifa->precio_final,
                    'descuento'      => $tarifa->descuentoVigente() ? $tarifa->descuento : 0,
                    'monto_original' => $tarifa->monto,
                ]);
            }
        }

        $sessionKeyPref = 'mp_pref_pago_' . $pago->id . '_' . (int) $pago->monto;
        $sessionKeyUrl  = 'mp_url_pago_' . $pago->id . '_' . (int) $pago->monto;
        $preferenceId   = session($sessionKeyPref);
        $checkoutUrl    = session($sessionKeyUrl);

        if (!$preferenceId || !$checkoutUrl) {
            try {
                $this->configurarMP();

                $preference = (new PreferenceClient())->create([
                    'items' => [[
                        'title'       => $this->tituloConcepto($pago),
                        'quantity'    => 1,
                        'unit_price'  => (float) $pago->monto,
                        'currency_id' => 'MXN',
                    ]],
                    'external_reference'   => 'PAGO-' . $pago->id,
                    'statement_descriptor'  => 'UICM ' . ucfirst($pago->concepto),
                    'auto_return'           => 'approved',
                    'back_urls'             => [
                        'success' => route('alumno.pagos.retorno', $pago),
                        'pending' => route('alumno.pagos.retorno', $pago),
                        'failure' => route('alumno.finanzas.index'),
                    ],
                ]);

                $preferenceId = $preference->id;
                $checkoutUrl  = app()->environment('production')
                    ? $preference->init_point
                    : $preference->sandbox_init_point;

                session([$sessionKeyPref => $preferenceId, $sessionKeyUrl => $checkoutUrl]);
                $pago->update(['mp_preference_id' => $preferenceId]);
            } catch (\MercadoPago\Exceptions\MPApiException $e) {
                Log::error('MP pagarAlumno MPApiException', [
                    'message'  => $e->getMessage(),
                    'httpCode' => $e->getApiResponse()?->getStatusCode(),
                    'body'     => $e->getApiResponse()?->getContent(),
                ]);
            } catch (\Exception $e) {
                Log::error('MP pagarAlumno error: ' . $e->getMessage());
            }
        }

        return view('alumno.pagos.pagar', compact('pago', 'checkoutUrl'));
    }

    public function retornoAlumno(Request $request, Pago $pago)
    {
        $paymentId = $request->query('payment_id');

        if ($paymentId) {
            try {
                $this->configurarMP();
                $payment = (new PaymentClient())->get((int) $paymentId);

                if (in_array($payment->status, ['approved', 'pending', 'in_process', 'authorized'])) {
                    $pago->update([
                        'mp_payment_id' => (string) $payment->id,
                        'fecha_pago'    => now()->toDateString(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('MP retornoAlumno error: ' . $e->getMessage());
            }
        }

        return redirect()->route('alumno.finanzas.index')
            ->with('success', 'Tu pago fue enviado y quedó en revisión por Finanzas.');
    }

    private function tituloConcepto(Pago $pago): string
    {
        $labels = [
            'colegiatura'   => 'Colegiatura',
            'cuatrimestre'  => 'Cuatrimestre',
            'reinscripcion' => 'Reinscripción',
        ];

        $titulo = 'UICM — ' . ($labels[$pago->concepto] ?? ucfirst($pago->concepto));

        return $pago->mes ? $titulo . ' ' . $pago->mes : $titulo;
    }

    // ─── Finanzas: listado y detalle ──────────────────────────────────────────

    public function index(Request $request)
    {
        $conteo = [
            'total'     => Pago::count(),
            'pendiente' => Pago::where('estado', 'pendiente')->count(),
            'aprobado'  => Pago::where('estado', 'aprobado')->count(),
            'rechazado' => Pago::where('estado', 'rechazado')->count(),
        ];

        $pagos = $this->pagosFiltrados($request)->paginate(50)->withQueryString();

        return view('finanzas.pagos.index', compact('pagos', 'conteo'));
    }

    public function exportar(Request $request)
    {
        $pagos = $this->pagosFiltrados($request)->get();

        $callback = function () use ($pagos) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Referencia', 'Nombre', 'Matricula', 'Programa', 'Concepto', 'Periodo', 'Monto', 'Descuento (%)', 'Fecha de pago', 'Estado']);

            foreach ($pagos as $pago) {
                fputcsv($handle, [
                    $pago->aspirante?->folio ?? $pago->alumno?->matricula ?? '',
                    $pago->aspirante?->nombre_completo ?? $pago->alumno?->nombre_completo ?? '',
                    $pago->alumno?->matricula ?? '',
                    $pago->aspirante?->programa?->nombre ?? $pago->alumno?->programa?->nombre ?? '',
                    $pago->concepto . ($pago->mes ? ' ' . $pago->mes : ''),
                    $pago->periodo,
                    $pago->monto,
                    $pago->descuento,
                    $pago->fecha_pago?->format('Y-m-d') ?? '',
                    $pago->estado,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pagos_' . now()->format('Ymd_His') . '.csv"',
        ]);
    }

    // ─── Finanzas: estadísticas ────────────────────────────────────────────────

    public function estadisticas(Request $request)
    {
        $filtrar = function ($query) use ($request) {
            return $query
                ->when($request->filled('concepto'), fn ($q) => $q->where('concepto', $request->concepto))
                ->when($request->filled('fecha_desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
                ->when($request->filled('fecha_hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta))
                ->when($request->filled('programa'), function ($q) use ($request) {
                    $q->where(function ($w) use ($request) {
                        $w->whereHas('aspirante', fn ($a) => $a->where('programa_id', $request->programa))
                          ->orWhereHas('alumno', fn ($al) => $al->where('programa_id', $request->programa));
                    });
                });
        };

        $aprobados  = $filtrar(Pago::where('estado', 'aprobado'))->get();
        $pendientes = $filtrar(Pago::where('estado', 'pendiente'))->get();
        $rechazados = $filtrar(Pago::where('estado', 'rechazado'))->get();

        $totalRecaudado = $aprobados->sum('monto');

        $porConcepto = $aprobados->groupBy('concepto')->map(fn ($grupo) => [
            'cantidad' => $grupo->count(),
            'monto'    => $grupo->sum('monto'),
        ]);

        $porMes = $aprobados->groupBy(fn (Pago $p) => $p->fecha_pago?->format('Y-m') ?? $p->created_at->format('Y-m'))
            ->sortKeys()
            ->map(fn ($grupo) => $grupo->sum('monto'));

        $montoPendiente   = $pendientes->sum('monto');
        $montoAtrasado    = $pendientes->filter(fn (Pago $p) => $p->estaVencido())->sum('monto');
        $montoRechazado   = $rechazados->sum('monto');

        $distribucionEstados = [
            'aprobado'  => $aprobados->count(),
            'pendiente' => $pendientes->count(),
            'rechazado' => $rechazados->count(),
        ];

        $programas = \App\Models\Programa::orderBy('nombre')->get();

        return view('finanzas.estadisticas', compact(
            'totalRecaudado', 'porConcepto', 'porMes', 'montoPendiente', 'montoAtrasado',
            'montoRechazado', 'distribucionEstados', 'pendientes', 'programas'
        ));
    }

    public function alumnosEstadoPago()
    {
        $alumnos = Alumno::where('estado', 'activo')
            ->with('programa')
            ->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get()
            ->map(function (Alumno $alumno) {
                $pendientes                = $alumno->todosLosPagos()->where('estado', 'pendiente');
                $atrasados                 = $pendientes->filter(fn (Pago $p) => $p->estaVencido());
                $alumno->montoAtrasado      = $atrasados->sum('monto');
                $alumno->cantidadAtrasados  = $atrasados->count();
                $alumno->alCorriente        = $atrasados->isEmpty();
                return $alumno;
            });

        $totalAtrasados   = $alumnos->where('alCorriente', false)->count();
        $totalAlCorriente = $alumnos->where('alCorriente', true)->count();
        $programas        = \App\Models\Programa::orderBy('nombre')->get();

        return view('finanzas.alumnos', compact('alumnos', 'totalAtrasados', 'totalAlCorriente', 'programas'));
    }

    private function pagosFiltrados(Request $request)
    {
        return Pago::with(['aspirante.programa', 'alumno.programa'])
            ->when($request->filled('concepto'), fn ($q) => $q->where('concepto', $request->concepto))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('fecha_desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta))
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->q;
                $query->where(function ($w) use ($texto) {
                    $w->whereHas('aspirante', fn ($a) => $a->where('folio', 'like', "%{$texto}%")
                            ->orWhere('nombre', 'like', "%{$texto}%")
                            ->orWhere('apellido_paterno', 'like', "%{$texto}%"))
                        ->orWhereHas('alumno', fn ($al) => $al->where('matricula', 'like', "%{$texto}%")
                            ->orWhere('nombre', 'like', "%{$texto}%")
                            ->orWhere('apellido_paterno', 'like', "%{$texto}%"));
                });
            })
            ->latest();
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

        // Si es pago de inscripción de un aspirante nuevo, generar matrícula y crear Alumno
        if ($pago->aspirante_id && !Alumno::where('aspirante_id', $pago->aspirante_id)->exists()) {
            $aspirante = $pago->aspirante->load('programa');
            $periodoObj = Periodo::where('nombre', $aspirante->generacion)->first();

            $matricula = $this->generarMatricula($aspirante);

            Alumno::create([
                'matricula'           => $matricula,
                'aspirante_id'        => $aspirante->id,
                'programa_id'         => $aspirante->programa_id,
                'periodo_id'          => $periodoObj?->id,
                'nombre'              => $aspirante->nombre,
                'apellido_paterno'    => $aspirante->apellido_paterno,
                'apellido_materno'    => $aspirante->apellido_materno,
                'email'               => $aspirante->email,
                'cuatrimestre_actual' => 1,
                'estado'              => 'activo',
            ]);
        }

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

    private function generarMatricula(Aspirante $aspirante): string
    {
        $periodo = Periodo::where('nombre', $aspirante->generacion)->first() ?? new Periodo();

        return Alumno::generarMatricula($periodo, $aspirante->programa);
    }

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

    private function obtenerTarifaInscripcion(?\App\Models\Programa $programa): ?\App\Models\TarifaInscripcion
    {
        $nivel = $programa?->nivel ?? 'licenciatura';
        return \App\Models\TarifaInscripcion::where('nivel', $nivel)
                    ->where('tipo', 'inscripcion')
                    ->first();
    }

    private function calcularMonto(?\App\Models\Programa $programa): float
    {
        $tarifa = $this->obtenerTarifaInscripcion($programa);

        if ($tarifa) {
            return $tarifa->precio_final;
        }

        $nivel = $programa?->nivel ?? 'licenciatura';
        return match($nivel) {
            'maestria'  => 4000.00,
            'doctorado' => 5000.00,
            default     => 3000.00,
        };
    }

    private function descuentoAplicado(?\App\Models\TarifaInscripcion $tarifa): float
    {
        return $tarifa && $tarifa->descuentoVigente() ? $tarifa->descuento : 0;
    }
}
