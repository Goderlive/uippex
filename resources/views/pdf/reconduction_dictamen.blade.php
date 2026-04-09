<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dictamen de Reconducción OSFEM</title>
    <style>
        @page {
            margin: 30px 40px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
            text-align: center;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-osfem { background-color: #800000; color: #ffffff; }
        .bg-gray { background-color: #f2f2f2; }
        
        /* Layout Header */
        .header-table { border: none; margin-bottom: 20px; }
        .header-table td { border: none; text-align: center; vertical-align: middle; }
        .header-logo-box {
            border: 1px solid #ccc;
            width: 120px; height: 60px;
            display: inline-block;
            color: #888; font-size: 9px;
            padding-top: 25px; box-sizing: border-box;
        }

        /* Titles */
        .main-title { font-size: 11px; font-weight: bold; margin: 0; padding: 0; }
        .sub-title { font-size: 10px; font-weight: bold; margin: 5px 0 0 0; }

        .form-info { font-size: 11px; border: none; }
        .form-info td { border: none; text-align: left; padding: 2px 5px; }

        .table-small-text { font-size: 8px; }
        
        /* Signature Area */
        .signatures-wrapper { margin-top: 30px; }
        .signatures-table { width: 100%; border: none; table-layout: fixed; }
        .signatures-table td { border: none; padding: 0 10px; vertical-align: bottom; height: 60px; }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            width: 100%; height: 40px;
        }
    </style>
</head>
<body>

    <!-- Header System -->
    <table class="header-table">
        <tr>
            <td width="20%">
                <div class="header-logo-box">LOGO H.<br>AYUNTAMIENTO</div>
            </td>
            <td width="60%">
                <p class="main-title">SISTEMA DE COORDINACIÓN HACENDARIA DEL ESTADO DE MÉXICO CON SUS MUNICIPIOS</p>
                <p class="sub-title">DICTAMEN DE RECONDUCCIÓN Y ACTUALIZACIÓN PROGRAMÁTICA - PRESUPUESTAL PARA RESULTADOS</p>
            </td>
            <td width="20%">
                <div class="header-logo-box">LOGO ORGANISMO</div>
            </td>
        </tr>
    </table>

    <!-- Document Info -->
    <table class="form-info">
        <tr>
            <td width="50%" class="font-bold">
                Tipo de Movimiento: (3) <br>
                <span style="font-weight:normal;">Modificación Programática</span>
            </td>
            <td width="50%" class="text-right font-bold" style="text-align: right;">
                No. de Oficio: (1) {{ $reconduction->document_number }} <br><br>
                Fecha: (2) {{ \Carbon\Carbon::parse($reconduction->requested_date)->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <!-- Identificación Áreas -->
    <table class="table-small-text" style="margin-top: 5px;">
        <tr>
            <td colspan="2" class="bg-osfem font-bold">Identificación del Área Solicitante</td>
        </tr>
        <tr>
            <td width="30%" class="text-left font-bold bg-gray">Dependencia General:</td>
            <td width="70%" class="text-left">{{ $reconduction->administrativeUnit->department->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td width="30%" class="text-left font-bold bg-gray">Unidad Administrativa (Auxiliar):</td>
            <td width="70%" class="text-left">{{ $reconduction->administrativeUnit->name ?? 'N/A' }}</td>
        </tr>
    </table>

    @php
        // Separaciones de ítems según dictamen OSFEM (Opcional, pero imprimiremos en bloque si reduce o si amplia)
        $reductions = $reconduction->items->whereIn('modification_type', ['reduction', 'cancellation']);
        $increases = $reconduction->items->whereIn('modification_type', ['increase', 'creation']);
    @endphp

    @if($reductions->count() > 0)
    <!-- REDUCCIONES / CANCELACIONES -->
    <table class="table-small-text" style="margin-top:15px;">
        <thead>
            <tr>
                <th colspan="10" class="sub-title text-left" style="background:#f9f9f9; padding:5px; border:none; border-bottom:1px solid #333;">
                    Metas de Actividad Programadas y alcanzadas del Proyecto a Cancelar o Reducir. (8)
                </th>
            </tr>
            <tr class="bg-osfem">
                <th width="8%" rowspan="2">Código</th>
                <th width="32%" rowspan="2">Descripción / Actividad</th>
                <th width="10%" rowspan="2">UM (Medida)</th>
                <th colspan="3">Cantidad Programada de la Meta</th>
                <th colspan="4">Calendarización Trimestral Modificada</th>
            </tr>
            <tr class="bg-osfem">
                <th width="8%">Inicial</th>
                <th width="8%">Avance</th>
                <th width="8%">Modificada</th>
                <th width="6%">1 (Mar)</th>
                <th width="6%">2 (Jun)</th>
                <th width="6%">3 (Sep)</th>
                <th width="6%">4 (Dic)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reductions as $item)
            @php
                $q1 = ($item->new_schedule['jan'] ?? 0) + ($item->new_schedule['feb'] ?? 0) + ($item->new_schedule['mar'] ?? 0);
                $q2 = ($item->new_schedule['apr'] ?? 0) + ($item->new_schedule['may'] ?? 0) + ($item->new_schedule['jun'] ?? 0);
                $q3 = ($item->new_schedule['jul'] ?? 0) + ($item->new_schedule['aug'] ?? 0) + ($item->new_schedule['sep'] ?? 0);
                $q4 = ($item->new_schedule['oct'] ?? 0) + ($item->new_schedule['nov'] ?? 0) + ($item->new_schedule['dec'] ?? 0);
            @endphp
            <tr>
                <td>{{ $item->activity->id }}</td>
                <td class="text-left">{{ $item->activity->name }}</td>
                <td>{{ $item->activity->measurement_unit ?? 'Unidad' }}</td>
                <td>{{ number_format($item->previous_annual_goal, 2) }}</td>
                <td>{{ number_format($item->achieved_so_far, 2) }}</td>
                <td class="font-bold">{{ number_format($item->new_annual_goal, 2) }}</td>
                <td>{{ number_format($q1, 2) }}</td>
                <td>{{ number_format($q2, 2) }}</td>
                <td>{{ number_format($q3, 2) }}</td>
                <td>{{ number_format($q4, 2) }}</td>
            </tr>
            <tr>
                <td colspan="10" class="text-left" style="background: #fdfdfd; padding: 6px;">
                    <b>Justificación Normativa:</b> {{ $item->justification }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($increases->count() > 0)
    <!-- INCREMENTOS / CREACIONES -->
    <table class="table-small-text" style="margin-top:15px;">
        <thead>
            <tr>
                <th colspan="10" class="sub-title text-left" style="background:#f9f9f9; padding:5px; border:none; border-bottom:1px solid #333;">
                    Metas de Actividad Programadas y alcanzadas del Proyecto que se Crea o Incrementa. (9)
                </th>
            </tr>
            <tr class="bg-osfem">
                <th width="8%" rowspan="2">Código</th>
                <th width="32%" rowspan="2">Descripción / Actividad</th>
                <th width="10%" rowspan="2">UM (Medida)</th>
                <th colspan="3">Cantidad Programada de la Meta</th>
                <th colspan="4">Calendarización Trimestral Modificada</th>
            </tr>
            <tr class="bg-osfem">
                <th width="8%">Inicial</th>
                <th width="8%">Avance</th>
                <th width="8%">Modificada</th>
                <th width="6%">1 (Mar)</th>
                <th width="6%">2 (Jun)</th>
                <th width="6%">3 (Sep)</th>
                <th width="6%">4 (Dic)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($increases as $item)
            @php
                $q1 = ($item->new_schedule['jan'] ?? 0) + ($item->new_schedule['feb'] ?? 0) + ($item->new_schedule['mar'] ?? 0);
                $q2 = ($item->new_schedule['apr'] ?? 0) + ($item->new_schedule['may'] ?? 0) + ($item->new_schedule['jun'] ?? 0);
                $q3 = ($item->new_schedule['jul'] ?? 0) + ($item->new_schedule['aug'] ?? 0) + ($item->new_schedule['sep'] ?? 0);
                $q4 = ($item->new_schedule['oct'] ?? 0) + ($item->new_schedule['nov'] ?? 0) + ($item->new_schedule['dec'] ?? 0);
            @endphp
            <tr>
                <td>{{ $item->activity->id }}</td>
                <td class="text-left">{{ $item->activity->name }}</td>
                <td>{{ $item->activity->measurement_unit ?? 'Unidad' }}</td>
                <td>{{ number_format($item->previous_annual_goal, 2) }}</td>
                <td>{{ number_format($item->achieved_so_far, 2) }}</td>
                <td class="font-bold">{{ number_format($item->new_annual_goal, 2) }}</td>
                <td>{{ number_format($q1, 2) }}</td>
                <td>{{ number_format($q2, 2) }}</td>
                <td>{{ number_format($q3, 2) }}</td>
                <td>{{ number_format($q4, 2) }}</td>
            </tr>
            <tr>
                <td colspan="10" class="text-left" style="background: #fdfdfd; padding: 6px;">
                    <b>Justificación Normativa:</b> {{ $item->justification }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Base Legal Footer -->
    <div style="font-size: 8px; margin-top:10px; text-transform:uppercase; color:#555;">
        CUANDO LAS ADECUACIONES APLIQUEN PARA MODIFICAR PRESUPUESTO, ESTAS SE DEBEN DEFINIR A NIVEL DE PARTIDA PRESUPUESTARIA Y CAPÍTULO DE GASTO EN RELACIÓN ANEXA. ESTO NO APLICA PARA ADECUACIONES PROGRAMÁTICAS PURAS, ES DECIR PARA MODIFICACIÓN DE PROGRAMACIÓN DE METAS DE ACTIVIDAD.
    </div>

    <!-- Signatures Panel -->
    <div class="signatures-wrapper">
        <table class="signatures-table">
            <tr>
                <td class="font-bold table-small-text">Elabora (Enlace o Dep. General)</td>
                <td class="font-bold table-small-text">Vo. Bo. (Tesorería / Finanzas)</td>
                <td class="font-bold table-small-text">Autorizó (Titular UIPPE / PMD)</td>
            </tr>
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <span class="table-small-text">{{ $reconduction->requestedBy->name ?? 'Enlace PBR' }}</span>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <span class="table-small-text">_______________________</span>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <span class="table-small-text">{{ $reconduction->validatedBy->name ?? 'Órgano Fiscalizador' }}</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
