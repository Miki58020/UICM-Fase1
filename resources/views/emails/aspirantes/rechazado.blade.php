<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de tu solicitud — UICM</title>
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
        <h1>Resultado de tu Solicitud</h1>
        <p class="header-sub">Notificación oficial de admisión</p>
        <div class="header-band"></div>
    </div>

    <div class="body">
        <p class="greeting">Estimado/a {{ $aspirante->nombre }} {{ $aspirante->apellido_paterno }},</p>

        <p>Después de revisar tu solicitud de ingreso a la <strong>Universidad Internacional Cuba México</strong>, lamentamos comunicarte que:</p>

        <div class="badge-wrap">
            <span class="badge">✕ &nbsp; SOLICITUD NO APROBADA</span>
        </div>

        @if($aspirante->observaciones)
        <div class="obs-box">
            <div class="obs-label">Motivo de la resolución</div>
            {{ $aspirante->observaciones }}
        </div>
        @endif

        <div class="info-box">
            Si consideras que existe un error en esta resolución o deseas mayor información, te invitamos a comunicarte directamente con el área de <strong>Control Escolar</strong> de la universidad.
        </div>

        <hr class="divider">

        <div class="signature">
            Atentamente,<br>
            <strong>Control Escolar — UICM</strong>
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
