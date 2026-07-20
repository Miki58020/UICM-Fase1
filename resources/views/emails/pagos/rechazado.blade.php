<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante no validado — UICM</title>
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
        .badge { display: inline-block; background: #7f1d1d; color: #fecaca; font-family: Arial, sans-serif; font-weight: bold; font-size: 15px; letter-spacing: 2px; padding: 10px 28px; border-radius: 30px; }

        .obs-box { background: #fff5f5; border-left: 4px solid #dc2626; border-radius: 0 8px 8px 0; padding: 18px 20px; margin: 20px 0; font-family: Arial, sans-serif; font-size: 14px; color: #555; font-style: italic; }
        .obs-label { font-style: normal; font-weight: bold; color: #dc2626; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }

        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #D4AF37; color: #fff; text-decoration: none; padding: 13px 32px; border-radius: 6px; font-family: Arial, sans-serif; font-weight: bold; font-size: 14px; letter-spacing: 0.5px; }

        .info-box { background: #f5faf7; border-left: 4px solid #0F4229; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 20px 0; font-family: Arial, sans-serif; font-size: 14px; color: #444; }

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
        <h1>Estado de tu Pago</h1>
        <p class="header-sub">Notificación sobre tu comprobante</p>
        <div class="header-band"></div>
    </div>

    <div class="body">
        @php
            $persona = $pago->aspirante ?? $pago->alumno;
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

        <p>Hemos revisado tu comprobante de {{ $conceptoLabel }} y lamentamos informarte que:</p>

        <div class="badge-wrap">
            <span class="badge">✕ &nbsp; COMPROBANTE NO VALIDADO</span>
        </div>

        @if($pago->observaciones)
        <div class="obs-box">
            <div class="obs-label">Motivo</div>
            {{ $pago->observaciones }}
        </div>
        @endif

        @if (!$esAlumno && $pago->aspirante)
        <p>Para continuar con tu proceso de inscripción, por favor sube un nuevo comprobante de pago:</p>

        <div class="btn-wrap">
            <a href="{{ route('aspirantes.pago', ['folio' => $pago->aspirante->folio]) }}" class="btn">Subir nuevo comprobante</a>
        </div>
        @else
        <p>Por favor comunícate con el área de <strong>Control Escolar</strong> para regularizar tu situación.</p>
        @endif

        <div class="info-box">
            Si tienes dudas sobre el proceso de pago o necesitas orientación, comunícate con el área de <strong>Finanzas</strong> de la universidad.
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
