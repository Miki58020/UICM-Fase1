<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al portal UICM</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #0F4229; padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #a7d7b8; margin: 6px 0 0; font-size: 14px; }
        .body { padding: 36px 40px; color: #333; font-size: 15px; line-height: 1.6; }
        .credentials { background: #f0f9f4; border: 2px solid #0F4229; border-radius: 8px; padding: 24px 28px; margin: 24px 0; }
        .credentials .row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #d1e7d9; }
        .credentials .row:last-child { border-bottom: none; }
        .credentials .label { font-size: 13px; color: #666; }
        .credentials .value { font-weight: bold; color: #0F4229; font-size: 16px; }
        .btn { display: inline-block; background: #0F4229; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .warning { background: #fffbeb; border-left: 4px solid #D4AF37; padding: 12px 16px; border-radius: 4px; font-size: 13px; color: #666; margin: 16px 0; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Universidad Internacional Cuba México</h1>
            <p>Bienvenido al portal estudiantil</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $alumno->nombre_completo }}</strong>.</p>
            <p>Tu inscripción ha sido completada exitosamente. Ya puedes acceder al portal estudiantil con las siguientes credenciales:</p>
            <div class="credentials">
                <div class="row">
                    <span class="label">Matrícula</span>
                    <span class="value">{{ $alumno->matricula }}</span>
                </div>
                <div class="row">
                    <span class="label">Correo de acceso</span>
                    <span class="value">{{ $alumno->email }}</span>
                </div>
                <div class="row">
                    <span class="label">Contraseña temporal</span>
                    <span class="value">{{ $password }}</span>
                </div>
                <div class="row">
                    <span class="label">Programa</span>
                    <span class="value">{{ $alumno->programa->nombre ?? '' }}</span>
                </div>
            </div>
            <div class="warning">
                Por seguridad, te recomendamos cambiar tu contraseña al ingresar por primera vez.
            </div>
            <p style="text-align:center;">
                <a href="{{ route('login') }}" class="btn">Ingresar al portal</a>
            </p>
            <p>Atentamente,<br><strong>Control Escolar — UICM</strong></p>
        </div>
        <div class="footer">
            Universidad Internacional Cuba México &bull; Todos los derechos reservados &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
