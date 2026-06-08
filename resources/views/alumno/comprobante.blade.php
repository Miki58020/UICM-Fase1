<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago | UICM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 32px 16px; color: #333; }
        .wrapper { max-width: 680px; margin: 0 auto; }

        .header { background: #0F4229; padding: 32px 40px; border-radius: 12px 12px 0 0; text-align: center; }
        .header-logo { font-size: 11px; letter-spacing: 3px; color: #D4AF37; text-transform: uppercase; margin-bottom: 8px; }
        .header h1 { color: #fff; font-size: 18px; font-weight: bold; }
        .header-band { height: 5px; background: linear-gradient(to right, #D4AF37, #f0d060, #D4AF37); margin-top: 20px; }

        .body { background: #fff; padding: 36px 40px; }

        .status-badge { text-align: center; margin: 0 0 28px; }
        .badge { display: inline-flex; align-items: center; gap: 8px; padding: 10px 28px; border-radius: 30px; font-size: 14px; font-weight: bold; }
        .badge-aprobado { background: #f0f9f4; color: #0F4229; border: 2px solid #0F4229; }
        .badge-pendiente { background: #fffbea; color: #b45309; border: 2px solid #D4AF37; }
        .badge-rechazado { background: #fef2f2; color: #991b1b; border: 2px solid #dc2626; }
        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .dot-aprobado { background: #0F4229; }
        .dot-pendiente { background: #D4AF37; }
        .dot-rechazado { background: #dc2626; }

        .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #888; margin-bottom: 12px; font-weight: bold; }

        .detail-box { background: #f8fafc; border-radius: 10px; padding: 4px 16px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        table tr { border-bottom: 1px solid #e5e7eb; }
        table tr:last-child { border-bottom: none; }
        table td { padding: 13px 8px; }
        table td:first-child { color: #6b7280; width: 45%; }
        table td:last-child { font-weight: bold; color: #111; text-align: right; }

        .mp-box { background: #fffbea; border-left: 4px solid #D4AF37; border-radius: 0 8px 8px 0; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #555; }
        .mp-box strong { color: #0F4229; }

        .actions { display: flex; gap: 12px; justify-content: center; margin-top: 28px; }
        .btn { display: inline-flex; align-items: center; gap-8px; padding: 11px 28px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; }
        .btn-primary { background: #0F4229; color: #fff; }
        .btn-secondary { background: #fff; color: #0F4229; border: 2px solid #0F4229; }

        .footer { background: #0F4229; padding: 18px 40px; text-align: center; border-radius: 0 0 12px 12px; }
        .footer p { font-size: 11px; color: #a7d7b8; line-height: 1.6; }
        .footer span { color: #D4AF37; }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .wrapper { max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">Universidad Internacional Cuba México</div>
        <h1>Comprobante de Pago</h1>
        <div class="header-band"></div>
    </div>

    <div class="body">

        {{-- Estado --}}
        <div class="status-badge">
            @if($pago->estado === 'aprobado')
                <span class="badge badge-aprobado">
                    <span class="dot dot-aprobado"></span> Pago aprobado
                </span>
            @elseif($pago->estado === 'pendiente')
                <span class="badge badge-pendiente">
                    <span class="dot dot-pendiente"></span> En revisión
                </span>
            @else
                <span class="badge badge-rechazado">
                    <span class="dot dot-rechazado"></span> Rechazado
                </span>
            @endif
        </div>

        {{-- Datos del alumno --}}
        <p class="section-title">Datos del alumno</p>
        <div class="detail-box">
            <table>
                <tr>
                    <td>Nombre</td>
                    <td>{{ $alumno->nombre_completo }}</td>
                </tr>
                <tr>
                    <td>Matrícula</td>
                    <td>{{ $alumno->matricula }}</td>
                </tr>
                <tr>
                    <td>Programa</td>
                    <td>{{ $alumno->programa->nombre ?? '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- Datos del pago --}}
        <p class="section-title">Datos del pago</p>
        <div class="detail-box">
            <table>
                <tr>
                    <td>Concepto</td>
                    <td style="text-transform: capitalize;">{{ $pago->concepto }}</td>
                </tr>
                <tr>
                    <td>Periodo</td>
                    <td>{{ $pago->periodo }}</td>
                </tr>
                <tr>
                    <td>Monto</td>
                    <td>
                        @if($pago->descuento > 0)
                            <span style="font-weight: normal; text-decoration: line-through; color: #9ca3af; margin-right: 6px;">
                                ${{ number_format($pago->monto_original, 2) }}
                            </span>
                        @endif
                        ${{ number_format($pago->monto, 2) }} MXN
                    </td>
                </tr>
                <tr>
                    <td>Fecha de pago</td>
                    <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- Nota de descuento aplicado --}}
        @if($pago->descuento > 0)
        <div class="mp-box">
            <strong>Descuento aplicado: {{ number_format($pago->descuento, 0) }}%</strong><br>
            Precio original ${{ number_format($pago->monto_original, 2) }} MXN — pagaste ${{ number_format($pago->monto, 2) }} MXN.
        </div>
        @endif

        {{-- Referencia Mercado Pago --}}
        @if($pago->mp_payment_id || $pago->mp_preference_id)
        <div class="mp-box">
            <strong>Referencia Mercado Pago</strong><br>
            @if($pago->mp_payment_id)
                ID de transacción: <strong>{{ $pago->mp_payment_id }}</strong><br>
            @endif
            @if($pago->mp_preference_id)
                ID de preferencia: <strong>{{ $pago->mp_preference_id }}</strong>
            @endif
        </div>
        @endif

        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">Imprimir comprobante</button>
        </div>

    </div>

    <div class="footer">
        <p>
            <span>Universidad Internacional Cuba México</span><br>
            &copy; {{ date('Y') }} Todos los derechos reservados.
        </p>
    </div>

</div>
</body>
</html>
