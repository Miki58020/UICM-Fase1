<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kárdex Académico | UICM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f3f4f6; padding: 32px 16px; color: #333; }
        .wrapper { max-width: 720px; margin: 0 auto; }

        /* Header */
        .header { background: #0F4229; padding: 32px 40px; border-radius: 12px 12px 0 0; text-align: center; }
        .header-logo { font-size: 11px; letter-spacing: 3px; color: #D4AF37; text-transform: uppercase; margin-bottom: 8px; }
        .header h1 { color: #fff; font-size: 20px; font-weight: bold; }
        .header-sub { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 4px; }
        .header-band { height: 5px; background: linear-gradient(to right, #D4AF37, #f0d060, #D4AF37); margin-top: 20px; }

        /* Body */
        .body { background: #fff; padding: 36px 40px; }

        /* Sección título */
        .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #888; margin-bottom: 10px; font-weight: bold; }

        /* Info del alumno */
        .alumno-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; margin-bottom: 28px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
        .alumno-grid .cell { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; }
        .alumno-grid .cell:nth-last-child(-n+2) { border-bottom: none; }
        .alumno-grid .cell:nth-child(odd) { border-right: 1px solid #e5e7eb; }
        .cell-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .cell-value { font-size: 13px; font-weight: bold; color: #111; }
        .cell-value.mono { font-family: 'Courier New', monospace; color: #0F4229; letter-spacing: 1px; }

        /* Barra de progreso créditos */
        .progress-wrap { background: #f8fafc; border-radius: 10px; padding: 16px 20px; margin-bottom: 28px; }
        .progress-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
        .progress-label { font-size: 12px; color: #555; font-weight: bold; }
        .progress-value { font-size: 13px; font-weight: bold; color: #0F4229; }
        .progress-bar-bg { background: #e5e7eb; border-radius: 99px; height: 10px; }
        .progress-bar-fill { background: linear-gradient(to right, #0F4229, #1a6b42); border-radius: 99px; height: 10px; transition: width 0.3s; }
        .progress-note { font-size: 11px; color: #9ca3af; margin-top: 6px; }

        /* Bloques por cuatrimestre */
        .cuatri-block { margin-bottom: 16px; page-break-inside: avoid; }
        .cuatri-header { display: flex; justify-content: space-between; align-items: baseline; background: #f8fafc; padding: 7px 14px; border-radius: 10px 10px 0 0; border: 1px solid #e5e7eb; border-bottom: none; }
        .cuatri-title { font-size: 11px; font-weight: 800; color: #0F4229; text-transform: uppercase; letter-spacing: 0.5px; }
        .cuatri-periodo { font-size: 10px; color: #9ca3af; }

        /* Tabla de materias */
        .table-wrap { border: 1px solid #e5e7eb; border-radius: 0 0 10px 10px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead tr { background: #0F4229; }
        thead th { padding: 6px 14px; text-align: left; color: #fff; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f9fafb; }
        tbody td { padding: 6px 14px; color: #333; }
        tbody td.center { text-align: center; }
        .materia-nombre { font-weight: bold; color: #111; }
        .materia-clave { font-size: 10px; color: #9ca3af; }

        .cal-aprobado { color: #166534; font-weight: bold; }
        .cal-reprobado { color: #991b1b; font-weight: bold; }
        .cal-pendiente { color: #d1d5db; }

        .badge { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: bold; }
        .badge-aprobado { background: #dcfce7; color: #166534; }
        .badge-reprobado { background: #fee2e2; color: #991b1b; }
        .badge-pendiente { background: #f3f4f6; color: #9ca3af; }

        /* Resumen */
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 28px; }
        .summary-card { background: #f8fafc; border-radius: 10px; padding: 14px 16px; text-align: center; }
        .summary-card .s-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 6px; }
        .summary-card .s-value { font-size: 22px; font-weight: 900; }
        .s-promedio { color: #0F4229; }
        .s-aprobadas { color: #166534; }
        .s-reprobadas { color: #991b1b; }
        .s-pendientes { color: #d97706; }

        /* Nota al pie */
        .nota { background: #fffbea; border-left: 4px solid #D4AF37; border-radius: 0 8px 8px 0; padding: 12px 16px; font-size: 12px; color: #666; margin-bottom: 24px; }

        /* Botones */
        .actions { display: flex; gap: 12px; justify-content: center; margin-top: 8px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 28px; border-radius: 8px; font-size: 13px; font-weight: bold; cursor: pointer; text-decoration: none; border: none; }
        .btn-primary { background: #0F4229; color: #fff; }
        .btn-secondary { background: #fff; color: #0F4229; border: 2px solid #0F4229; }

        /* Footer */
        .footer { background: #0F4229; padding: 18px 40px; text-align: center; border-radius: 0 0 12px 12px; }
        .footer p { font-size: 11px; color: #a7d7b8; line-height: 1.6; }
        .footer span { color: #D4AF37; }

        @media print {
            @page { margin: 8mm; size: A4 portrait; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .wrapper { max-width: 100%; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="header">
        <div class="header-logo">Universidad Internacional Cuba México</div>
        <h1>Kárdex Académico</h1>
        <p class="header-sub">Documento generado el {{ now()->format('d/m/Y') }}</p>
        <div class="header-band"></div>
    </div>

    <div class="body">

        {{-- Datos del alumno --}}
        <p class="section-title">Datos del alumno</p>
        <div class="alumno-grid">
            <div class="cell">
                <div class="cell-label">Nombre completo</div>
                <div class="cell-value">{{ $alumno->nombre_completo }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Matrícula</div>
                <div class="cell-value mono">{{ $alumno->matricula }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Programa</div>
                <div class="cell-value">{{ $alumno->programa->nombre ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Nivel</div>
                <div class="cell-value">{{ ucfirst($alumno->programa->nivel ?? '—') }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Grupo</div>
                <div class="cell-value">{{ $alumno->grupo->clave ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Periodo</div>
                <div class="cell-value">{{ $alumno->grupo->periodo->label ?? $alumno->periodo->label ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Cuatrimestre actual</div>
                <div class="cell-value">{{ $alumno->cuatrimestre_actual }}°</div>
            </div>
            <div class="cell">
                <div class="cell-label">Estado</div>
                <div class="cell-value">{{ ucfirst($alumno->estado) }}</div>
            </div>
        </div>

        {{-- Progreso de créditos --}}
        @if($totalCreditosPrograma > 0)
        <p class="section-title">Avance de créditos</p>
        @php $pct = min(100, $totalCreditosPrograma > 0 ? round(($alumno->creditos_acumulados / $totalCreditosPrograma) * 100) : 0); @endphp
        <div class="progress-wrap">
            <div class="progress-header">
                <span class="progress-label">Créditos acumulados</span>
                <span class="progress-value">{{ $alumno->creditos_acumulados }} / {{ $totalCreditosPrograma }}</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $pct }}%;"></div>
            </div>
            <p class="progress-note">{{ $pct }}% completado · Duración del programa: {{ $alumno->programa->duracion_cuatrimestres ?? '—' }} cuatrimestres</p>
        </div>
        @endif

        {{-- Resumen estadístico --}}
        <p class="section-title">Resumen general</p>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="s-label">Promedio</div>
                <div class="s-value s-promedio">{{ $promedioGeneral !== null ? number_format($promedioGeneral, 1) : '—' }}</div>
            </div>
            <div class="summary-card">
                <div class="s-label">Aprobadas</div>
                <div class="s-value s-aprobadas">{{ $aprobadas }}</div>
            </div>
            <div class="summary-card">
                <div class="s-label">Reprobadas</div>
                <div class="s-value s-reprobadas">{{ $reprobadas }}</div>
            </div>
            <div class="summary-card">
                <div class="s-label">Pendientes</div>
                <div class="s-value s-pendientes">{{ $pendientes }}</div>
            </div>
        </div>

        {{-- Tabla de materias por cuatrimestre --}}
        <p class="section-title">Historial de calificaciones</p>
        @if($porCuatrimestre->isEmpty())
            <div class="nota">Aún no hay materias asignadas.</div>
        @else
            @foreach($porCuatrimestre as $numCuatri => $items)
            <div class="cuatri-block">
                <div class="cuatri-header">
                    <span class="cuatri-title">{{ $numCuatri > 0 ? $numCuatri.'° Cuatrimestre' : 'Sin cuatrimestre asignado' }}</span>
                    <span class="cuatri-periodo">{{ $items->first()['carga']->periodo->label ?? '' }}</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th class="center">Créditos</th>
                                <th class="center">Parcial 1</th>
                                <th class="center">Parcial 2</th>
                                <th class="center">Extraord.</th>
                                <th class="center">Final</th>
                                <th class="center">Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $m)
                            @php
                                $c = $m['carga'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="materia-nombre">{{ $c->materia->nombre }}</div>
                                    <div class="materia-clave">{{ $c->materia->clave }}</div>
                                </td>
                                <td class="center">
                                    <span style="font-weight:bold; color:#D4AF37;">{{ $c->materia->creditos }}</span>
                                </td>
                                <td class="center">
                                    @if($m['p1'])
                                        <span class="{{ $m['p1']->calificacion >= 7.0 ? 'cal-aprobado' : 'cal-reprobado' }}">
                                            {{ number_format($m['p1']->calificacion, 1) }}
                                        </span>
                                    @else
                                        <span class="cal-pendiente">—</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($m['p2'])
                                        <span class="{{ $m['p2']->calificacion >= 7.0 ? 'cal-aprobado' : 'cal-reprobado' }}">
                                            {{ number_format($m['p2']->calificacion, 1) }}
                                        </span>
                                    @else
                                        <span class="cal-pendiente">—</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($m['ex'])
                                        <span class="{{ $m['ex']->calificacion >= 7.0 ? 'cal-aprobado' : 'cal-reprobado' }}">
                                            {{ number_format($m['ex']->calificacion, 1) }}
                                        </span>
                                    @else
                                        <span class="cal-pendiente">—</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($m['calFinal'] !== null)
                                        <span class="{{ $m['aprobado'] ? 'cal-aprobado' : 'cal-reprobado' }}">
                                            {{ number_format($m['calFinal'], 1) }}
                                        </span>
                                    @else
                                        <span class="cal-pendiente">—</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($m['calFinal'] === null)
                                        <span class="badge badge-pendiente">Pendiente</span>
                                    @elseif($m['aprobado'])
                                        <span class="badge badge-aprobado">Aprobada</span>
                                    @else
                                        <span class="badge badge-reprobado">Reprobada</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        @endif

        <div class="nota">
            Calificación mínima aprobatoria: <strong>7.0</strong>.
            La calificación final es el promedio de los dos parciales. Si se presentó extraordinario, este sustituye la calificación final.
        </div>

        <div class="actions">
            <a href="{{ route('alumno.dashboard') }}" class="btn btn-secondary">← Volver al portal</a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir
            </button>
        </div>

    </div>

    <div class="footer">
        <p>
            <span>Universidad Internacional Cuba México</span><br>
            Este documento es de carácter informativo. Para uso oficial solicita el kárdex sellado en Control Escolar.<br>
            &copy; {{ date('Y') }} Todos los derechos reservados.
        </p>
    </div>

</div>
</body>
</html>
