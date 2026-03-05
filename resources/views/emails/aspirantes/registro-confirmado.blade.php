<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de registro</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #0F4229; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #a7d7b8; margin: 6px 0 0; font-size: 14px; }
        .body { padding: 36px 40px; color: #333; font-size: 15px; line-height: 1.6; }
        .folio-box { background: #f0f9f4; border: 2px solid #0F4229; border-radius: 8px; text-align: center; padding: 20px; margin: 24px 0; }
        .folio-box .label { font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .folio-box .folio { font-size: 28px; font-weight: bold; color: #0F4229; letter-spacing: 2px; margin-top: 6px; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Universidad Internacional Cuba México</h1>
            <p>Confirmación de registro</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $aspirante->nombre }} {{ $aspirante->apellido_paterno }}</strong>.</p>
            <p>Hemos recibido tu solicitud de ingreso correctamente. Tu folio de seguimiento es:</p>
            <div class="folio-box">
                <div class="label">Folio de seguimiento</div>
                <div class="folio">{{ $aspirante->folio }}</div>
            </div>
            <p>Guarda este folio. Puedes usarlo en cualquier momento para consultar el estatus de tu solicitud en:</p>
            <p style="text-align:center;">
                <a href="{{ route('aspirantes.seguimiento') }}" style="color:#0F4229; font-weight:bold;">
                    {{ route('aspirantes.seguimiento') }}
                </a>
            </p>
            <p>Tu solicitud será revisada en un plazo de 3 a 5 días hábiles. Te notificaremos por este mismo correo cuando haya una actualización.</p>
            <p>Atentamente,<br><strong>Control Escolar — UICM</strong></p>
        </div>
        <div class="footer">
            Universidad Internacional Cuba México &bull; Todos los derechos reservados &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
