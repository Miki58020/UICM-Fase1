<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respuesta a tu mensaje — UICM</title>
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

        .interes-badge { display: inline-block; background: #D4AF37; color: #0F4229; font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 3px 12px; border-radius: 20px; margin-bottom: 14px; }

        .respuesta-box { background: #f5faf7; border-left: 4px solid #0F4229; border-radius: 0 8px 8px 0; padding: 18px 22px; margin: 20px 0; font-family: Arial, sans-serif; font-size: 15px; color: #1a1a1a; line-height: 1.7; white-space: pre-line; }

        .original-wrap { margin-top: 28px; }
        .original-label { font-family: Arial, sans-serif; font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .original-box { background: #f9f9f9; border-left: 3px solid #ccc; border-radius: 0 6px 6px 0; padding: 12px 16px; font-family: Arial, sans-serif; font-size: 13px; color: #666; line-height: 1.6; white-space: pre-line; }

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
        <h1>Respuesta a tu mensaje</h1>
        <p class="header-sub">{{ now()->translatedFormat('l d \d\e F \d\e Y') }}</p>
        <div class="header-band"></div>
    </div>

    <div class="body">

        @php
            $interesLabels = [
                'oferta'     => 'Oferta educativa',
                'admisiones' => 'Proceso de admisión',
                'estatus'    => 'Estatus de solicitud',
                'plataforma' => 'Plataforma académica',
                'otros'      => 'Otros',
            ];
        @endphp

        <span class="interes-badge">{{ $interesLabels[$contacto->interes] ?? $contacto->interes }}</span>

        <p class="greeting">Hola, {{ $contacto->nombre }}.</p>

        <p style="font-family: Arial, sans-serif; font-size: 14px; color: #555; margin-bottom: 6px;">
            Hemos recibido tu mensaje y a continuación encontrarás nuestra respuesta:
        </p>

        <div class="respuesta-box">{{ $respuesta }}</div>

        @if (!empty($contacto->mensaje))
        <div class="original-wrap">
            <p class="original-label">Tu mensaje original</p>
            <div class="original-box">{{ $contacto->mensaje }}</div>
        </div>
        @endif

        <hr class="divider">

        <div class="signature">
            Atentamente,<br>
            <strong>Universidad Internacional Cuba México</strong><br>
            <span style="font-size: 12px; color: #888;">Si tienes más dudas, no dudes en escribirnos nuevamente.</span>
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
