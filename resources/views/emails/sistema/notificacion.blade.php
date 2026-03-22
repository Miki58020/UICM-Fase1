<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
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

        .rol-badge { display: inline-block; background: #D4AF37; color: #0F4229; font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 3px 12px; border-radius: 20px; margin-bottom: 14px; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo">Universidad Internacional Cuba México</div>
        <h1>{{ $titulo }}</h1>
        <p class="header-sub">Notificación del sistema — {{ now()->format('d/m/Y H:i') }}</p>
        <div class="header-band"></div>
    </div>

    <div class="body">

        @php
            $rolLabels = [
                'admin'           => 'Administrador',
                'control_escolar' => 'Control Escolar',
                'finanzas'        => 'Finanzas',
                'coordinacion'    => 'Coordinación Académica',
            ];
        @endphp

        <div>
            <span class="rol-badge">{{ $rolLabels[$destinatario->rol] ?? $destinatario->rol }}</span>
        </div>

        <p class="greeting">Estimado/a {{ $destinatario->nombre_completo }},</p>

        <p>{{ $mensaje }}</p>

        @if(!empty($detalles))
        <div class="detail-wrap">
            <table class="detail-table">
                @foreach($detalles as $clave => $valor)
                <tr>
                    <td>{{ $clave }}</td>
                    <td>{{ $valor }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        @if($accionUrl && $accionLabel)
        <div class="btn-wrap">
            <a href="{{ $accionUrl }}" class="btn">{{ $accionLabel }}</a>
        </div>
        @endif

        <div class="info-box">
            Este es un mensaje automático del sistema UICM. No responda a este correo.
        </div>

        <hr class="divider">

        <div class="signature">
            Atentamente,<br>
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
