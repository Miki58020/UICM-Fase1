<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de cambio de contraseña</title>
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

        .rol-badge { display: inline-block; background: #D4AF37; color: #0F4229; font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 3px 12px; border-radius: 20px; margin-bottom: 14px; }

        .detail-wrap { background: #f5faf7; border-radius: 10px; padding: 4px 16px; margin: 16px 0 24px; }
        .detail-table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; }
        .detail-table tr { border-bottom: 1px solid #e0ede5; }
        .detail-table tr:last-child { border-bottom: none; }
        .detail-table td { padding: 14px 8px; }
        .detail-table td:first-child { color: #555; width: 40%; }
        .detail-table td:last-child { font-weight: bold; color: #0F4229; }

        .info-box { background: #fffbea; border-left: 4px solid #D4AF37; border-radius: 0 8px 8px 0; padding: 16px 20px; margin: 20px 0; font-family: Arial, sans-serif; font-size: 14px; color: #555; }

        .btn-wrap { text-align: center; margin: 28px 0 8px; }
        .btn { display: inline-block; background: #0F4229; color: #fff; text-decoration: none; padding: 13px 36px; border-radius: 8px; font-family: Arial, sans-serif; font-weight: bold; font-size: 14px; letter-spacing: 0.5px; }

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
        <h1>Solicitud de cambio de contraseña</h1>
        <p class="header-sub">{{ now()->translatedFormat('l d \d\e F \d\e Y') }}</p>
        <div class="header-band"></div>
    </div>

    <div class="body">

        @php
            $rolLabels = [
                'admin'           => 'Administrador',
                'control_escolar' => 'Control Escolar',
                'finanzas'        => 'Finanzas',
                'coordinacion'    => 'Coordinación Académica',
                'alumno'          => 'Alumno',
            ];
        @endphp

        <div>
            <span class="rol-badge">{{ $rolLabels[$solicitante->rol] ?? $solicitante->rol }}</span>
        </div>

        <p class="greeting">Se ha recibido una solicitud de cambio de contraseña.</p>

        <p>Un usuario del sistema ha solicitado el restablecimiento de su contraseña. A continuación encontrarás los datos del solicitante:</p>

        <div class="detail-wrap">
            <table class="detail-table">
                <tr>
                    <td>Nombre completo</td>
                    <td>{{ $solicitante->nombre_completo }}</td>
                </tr>
                <tr>
                    <td>Correo electrónico</td>
                    <td>{{ $solicitante->email }}</td>
                </tr>
                <tr>
                    <td>Rol</td>
                    <td>{{ $rolLabels[$solicitante->rol] ?? $solicitante->rol }}</td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            Para atender esta solicitud, ingresa al panel de administración y asigna una nueva contraseña al usuario.
        </div>

        <div class="btn-wrap">
            <a href="{{ route('admin.solicitudes-contrasena.index') }}" class="btn">
                Ir al panel de solicitudes
            </a>
        </div>

        <hr class="divider">

        <div class="signature">
            Saludos,<br>
            <strong>Sistema de Gestión Escolar — UICM</strong>
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
