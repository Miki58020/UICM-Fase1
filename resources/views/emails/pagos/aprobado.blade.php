<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago aprobado</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #0F4229; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #a7d7b8; margin: 6px 0 0; font-size: 14px; }
        .badge { display: inline-block; background: #16a34a; color: #fff; font-weight: bold; border-radius: 20px; padding: 6px 20px; font-size: 14px; margin: 16px 0; }
        .body { padding: 36px 40px; color: #333; font-size: 15px; line-height: 1.6; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .info-row:last-child { border-bottom: none; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Universidad Internacional Cuba México</h1>
            <p>Confirmación de pago</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $pago->aspirante->nombre }} {{ $pago->aspirante->apellido_paterno }}</strong>.</p>
            <p>Tu pago de inscripción ha sido <span class="badge">APROBADO</span></p>
            <p>Detalles del pago:</p>
            <div style="background:#f9f9f9; border-radius:6px; padding:16px 20px; margin:16px 0;">
                <div class="info-row"><span style="color:#666;">Concepto</span><strong>Pago de inscripción</strong></div>
                <div class="info-row"><span style="color:#666;">Monto</span><strong>${{ number_format($pago->monto, 2) }} MXN</strong></div>
                <div class="info-row"><span style="color:#666;">Fecha</span><strong>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</strong></div>
                <div class="info-row"><span style="color:#666;">Folio</span><strong>{{ $pago->aspirante->folio }}</strong></div>
            </div>
            <p>El área de Control Escolar finalizará tu proceso de inscripción y recibirás tus credenciales de acceso al portal en los próximos días hábiles.</p>
            <p>Atentamente,<br><strong>Finanzas — UICM</strong></p>
        </div>
        <div class="footer">
            Universidad Internacional Cuba México &bull; Todos los derechos reservados &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
