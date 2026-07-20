<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago validado — UICM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Georgia', serif; background: #eef2ee; padding: 32px 16px; }
        .wrapper { max-width: 600px; margin: 0 auto; }

        .header { background: #0F4229; padding: 36px 40px 0; text-align: center; border-radius: 12px 12px 0 0; }
        .header-logo { font-size: 11px; font-family: Arial, sans-serif; letter-spacing: 3px; color: #D4AF37; text-transform: uppercase; margin-bottom: 10px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: normal; letter-spacing: 0.5px; line-height: 1.4; }
        .header-sub { font-family: Arial, sans-serif; color: #a7d7b8; font-size: 13px; margin-top: 6px; }
        .header-band { height: 6px; background: linear-gradient(to right, #D4AF37, #f0d060, #D4AF37); margin-top: 24px; }

        .body { background: #fff; padding: 40px; color: #333; font-size: 15px; line-height: 1.7; }
        .greeting { font-size: 17px; color: #0F4229; font-weight: bold; margin-bottom: 16px; }
        .divider { border: none; border-top: 1px solid #e8e8e8; margin: 24px 0; }

        .badge-wrap { text-align: center; margin: 24px 0; }
        .badge { display: inline-block; background: #0F4229; color: #D4AF37; font-family: Arial, sans-serif; font-weight: bold; font-size: 15px; letter-spacing: 2px; padding: 10px 28px; border-radius: 30px; border: 2px solid #D4AF37; }

        /* Tabla de detalles */
        .detail-table { width: 100%; border-collapse: collapse; margin: 24px 0; font-family: Arial, sans-serif; font-size: 14px; }
        .detail-table tr { border-bottom: 1px solid #eee; }
        .detail-table tr:last-child { border-bottom: none; }
        .detail-table td { padding: 12px 8px; }
        .detail-table td:first-child { color: #888; width: 45%; }
        .detail-table td:last-child { font-weight: bold; color: #1a1a1a; text-align: right; }
        .detail-table .monto td:last-child { color: #0F4229; font-size: 18px; }
        .detail-wrap { background: #f5faf7; border-radius: 10px; padding: 4px 16px; margin: 24px 0; }

        .info-box { background: #fffbea; border-left: 4px solid #D4AF37; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 20px 0; font-family: Arial, sans-serif; font-size: 14px; color: #555; }

        .signature { margin-top: 28px; font-family: Arial, sans-serif; font-size: 14px; color: #555; }
        .signature strong { color: #0F4229; }

        .footer { background: #0F4229; padding: 20px 40px; text-align: center; border-radius: 0 0 12px 12px; }
        .footer p { font-family: Arial, sans-serif; font-size: 11px; color: #a7d7b8; line-height: 1.6; }
        .footer span { color: #D4AF37; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">Universidad Internacional Cuba México</div>
        <h1>Confirmación de Pago</h1>
        <p class="header-sub">Tu comprobante ha sido validado</p>
        <div class="header-band"></div>
    </div>

    <div class="body">
        @php
            $persona = $pago->aspirante ?? $pago->alumno;
            $ref = $pago->aspirante?->folio ?? ($pago->alumno ? $pago->alumno->matricula : '—');
            // La distinción alumno/aspirante depende de a quién pertenece el pago, no del concepto:
            // colegiatura y reinscripción son ambos pagos de alumno, igual que inscripción es de aspirante.
            $esAlumno = (bool) $pago->alumno_id;
            $labelsConcepto = [
                'inscripcion'  => 'inscripción',
                'colegiatura'  => 'colegiatura',
                'cuatrimestre' => 'reinscripción',
                'otro'         => 'pago',
            ];
            $conceptoLabel = $labelsConcepto[$pago->concepto] ?? 'pago';
        @endphp
        <p class="greeting">Estimado/a {{ $persona?->nombre }} {{ $persona?->apellido_paterno }},</p>

        <p>Tu comprobante de {{ $conceptoLabel }} ha sido revisado y:</p>

        <div class="badge-wrap">
            <span class="badge">✓ &nbsp; PAGO VALIDADO</span>
        </div>

        <div class="detail-wrap">
            <table class="detail-table">
                <tr>
                    <td>{{ $esAlumno ? 'Matrícula' : 'Folio de solicitud' }}</td>
                    <td>{{ $ref }}</td>
                </tr>
                <tr>
                    <td>Concepto</td>
                    <td>Pago de {{ $conceptoLabel }}</td>
                </tr>
                <tr>
                    <td>Fecha de pago</td>
                    <td>{{ $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') : '—' }}</td>
                </tr>
                <tr class="monto">
                    <td>Monto</td>
                    <td>${{ number_format($pago->monto, 2) }} MXN</td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            @if (!$esAlumno)
                El área de <strong>Control Escolar</strong> finalizará tu proceso de inscripción. En los próximos días hábiles recibirás tus credenciales de acceso al portal estudiantil.
            @elseif ($pago->concepto === 'cuatrimestre')
                El área de <strong>Control Escolar</strong> finalizará tu proceso de reinscripción y te asignará grupo para el nuevo período.
            @else
                Gracias por tu pago, ya quedó reflejado en tu estado de cuenta dentro del portal.
            @endif
        </div>

        <hr class="divider">

        <div class="signature">
            Atentamente,<br>
            <strong>Finanzas — UICM</strong>
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
