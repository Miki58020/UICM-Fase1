<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de registro — UICM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Georgia', serif; background: #eef2ee; padding: 32px 16px; }
        .wrapper { max-width: 600px; margin: 0 auto; }

        /* Header */
        .header { background: #0F4229; padding: 36px 40px 0; text-align: center; border-radius: 12px 12px 0 0; }
        .header-logo { font-size: 11px; font-family: Arial, sans-serif; letter-spacing: 3px; color: #D4AF37; text-transform: uppercase; margin-bottom: 10px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: normal; letter-spacing: 0.5px; line-height: 1.4; }
        .header-sub { font-family: Arial, sans-serif; color: #a7d7b8; font-size: 13px; margin-top: 6px; }
        .header-band { height: 6px; background: linear-gradient(to right, #D4AF37, #f0d060, #D4AF37); margin-top: 24px; }

        /* Body */
        .body { background: #fff; padding: 40px; color: #333; font-size: 15px; line-height: 1.7; }
        .greeting { font-size: 17px; color: #0F4229; font-weight: bold; margin-bottom: 16px; }
        .divider { border: none; border-top: 1px solid #e8e8e8; margin: 24px 0; }

        /* Folio box */
        .folio-box { background: #f5faf7; border: 2px solid #D4AF37; border-radius: 10px; text-align: center; padding: 24px 20px; margin: 28px 0; }
        .folio-label { font-family: Arial, sans-serif; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 2px; }
        .folio-value { font-family: 'Courier New', monospace; font-size: 30px; font-weight: bold; color: #0F4229; letter-spacing: 3px; margin: 8px 0 0; }
        .folio-hint { font-family: Arial, sans-serif; font-size: 12px; color: #aaa; margin-top: 6px; }

        /* Info box */
        .info-box { background: #f5faf7; border-left: 4px solid #0F4229; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 20px 0; font-family: Arial, sans-serif; font-size: 14px; color: #444; }

        /* CTA button */
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #D4AF37; color: #fff; text-decoration: none; padding: 13px 32px; border-radius: 6px; font-family: Arial, sans-serif; font-weight: bold; font-size: 14px; letter-spacing: 0.5px; }

        .signature { margin-top: 28px; font-family: Arial, sans-serif; font-size: 14px; color: #555; }
        .signature strong { color: #0F4229; }

        /* Footer */
        .footer { background: #0F4229; padding: 20px 40px; text-align: center; border-radius: 0 0 12px 12px; }
        .footer p { font-family: Arial, sans-serif; font-size: 11px; color: #a7d7b8; line-height: 1.6; }
        .footer span { color: #D4AF37; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">Universidad Internacional Cuba México</div>
        <h1>Confirmación de Registro</h1>
        <p class="header-sub">Solicitud de ingreso recibida correctamente</p>
        <div class="header-band"></div>
    </div>

    <div class="body">
        <p class="greeting">Estimado/a {{ $aspirante->nombre }} {{ $aspirante->apellido_paterno }},</p>

        <p>Hemos recibido tu solicitud de ingreso a la <strong>Universidad Internacional Cuba México</strong>. A continuación encontrarás tu folio personal de seguimiento:</p>

        <div class="folio-box">
            <div class="folio-label">Folio de seguimiento</div>
            <div class="folio-value">{{ $aspirante->folio }}</div>
            <div class="folio-hint">Consérvalo — lo necesitarás para consultar tu estatus</div>
        </div>

        <div class="info-box">
            Tu solicitud será revisada en un plazo de <strong>3 a 5 días hábiles</strong>. Recibirás una notificación por este mismo correo cuando haya una resolución.
        </div>

        <p>Puedes consultar el estado de tu solicitud en cualquier momento:</p>

        <div class="btn-wrap">
            <a href="{{ route('aspirantes.seguimiento') }}" class="btn">Consultar estatus de mi solicitud</a>
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
