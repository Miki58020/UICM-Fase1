<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago rechazado</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #0F4229; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #a7d7b8; margin: 6px 0 0; font-size: 14px; }
        .badge { display: inline-block; background: #dc2626; color: #fff; font-weight: bold; border-radius: 20px; padding: 6px 20px; font-size: 14px; margin: 16px 0; }
        .body { padding: 36px 40px; color: #333; font-size: 15px; line-height: 1.6; }
        .obs-box { background: #fff5f5; border-left: 4px solid #dc2626; padding: 16px 20px; border-radius: 4px; margin: 20px 0; color: #555; font-style: italic; }
        .btn { display: inline-block; background: #0F4229; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Universidad Internacional Cuba México</h1>
            <p>Estado de tu pago</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $pago->aspirante->nombre }} {{ $pago->aspirante->apellido_paterno }}</strong>.</p>
            <p>Lamentamos informarte que tu comprobante de pago no pudo ser validado y ha sido <span class="badge">RECHAZADO</span></p>
            @if($pago->observaciones)
            <p>Motivo:</p>
            <div class="obs-box">{{ $pago->observaciones }}</div>
            @endif
            <p>Por favor, sube un nuevo comprobante de pago desde el portal:</p>
            <p style="text-align:center;">
                <a href="{{ route('aspirantes.pago', ['folio' => $pago->aspirante->folio]) }}" class="btn">
                    Volver a subir comprobante
                </a>
            </p>
            <p>Atentamente,<br><strong>Finanzas — UICM</strong></p>
        </div>
        <div class="footer">
            Universidad Internacional Cuba México &bull; Todos los derechos reservados &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
