<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al portal UICM</title>
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

        .detail-wrap { background: #f5faf7; border-radius: 10px; padding: 4px 16px; margin: 24px 0; }
        .detail-table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; }
        .detail-table tr { border-bottom: 1px solid #e0ede5; }
        .detail-table tr:last-child { border-bottom: none; }
        .detail-table td { padding: 13px 8px; }
        .detail-table td:first-child { color: #888; width: 45%; }
        .detail-table td:last-child { font-weight: bold; color: #0F4229; text-align: right; font-size: 15px; }

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
        <h1>Bienvenido al Portal Estudiantil</h1>
        <p class="header-sub">Tu inscripción ha sido completada</p>
        <div class="header-band"></div>
    </div>

    <div class="body">
        <p class="greeting">Estimado/a {{ $alumno->nombre_completo }},</p>

        <p>Tu proceso de inscripción ha sido completado exitosamente. A continuación encontrarás tus credenciales de acceso al portal estudiantil:</p>

        <div class="badge-wrap">
            <span class="badge">✓ &nbsp; ACCESO ACTIVADO</span>
        </div>

        <div class="detail-wrap">
            <table class="detail-table">
                <tr>
                    <td>Matrícula</td>
                    <td>{{ $alumno->matricula }}</td>
                </tr>
                <tr>
                    <td>Correo de acceso</td>
                    <td>{{ $alumno->user->email ?? $alumno->email }}</td>
                </tr>
                <tr>
                    <td>Contraseña temporal</td>
                    <td>{{ $password }}</td>
                </tr>
                <tr>
                    <td>Programa</td>
                    <td>{{ $alumno->programa->nombre ?? '—' }}</td>
                </tr>
            </table>
        </div>

        <div class="info-box">
            Por seguridad, te recomendamos <strong>cambiar tu contraseña</strong> al ingresar por primera vez al portal.
        </div>

        <div class="btn-wrap">
            <a href="{{ route('login') }}" class="btn">Ingresar al portal</a>
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
