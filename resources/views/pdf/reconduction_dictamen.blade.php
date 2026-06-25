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
                @if(isset($config) && $config->shield_path)
                    <img src="{{ storage_path('app/public/' . $config->shield_path) }}" style="max-width: 120px; max-height: 80px;">
                @else
                    <div class="header-logo-box">LOGO H.<br>AYUNTAMIENTO</div>
                @endif
            </td>
            <td width="60%">
                <p class="main-title">SISTEMA DE COORDINACIÓN HACENDARIA DEL ESTADO DE MÉXICO CON SUS MUNICIPIOS</p>
                <p class="sub-title">DICTAMEN DE RECONDUCCIÓN Y ACTUALIZACIÓN PROGRAMÁTICA - PRESUPUESTAL PARA RESULTADOS</p>
            </td>
            <td width="20%">
                @if(isset($config) && $config->logo_path)
                    <img src="{{ storage_path('app/public/' . $config->logo_path) }}" style="max-width: 120px; max-height: 80px;">
                @else
                    <div class="header-logo-box">LOGO ORGANISMO</div>
                @endif
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
    <table style="width: 100%; border: none; margin-top: 5px; table-layout: fixed;">
        <tr>
            <!-- Lado Izquierdo (Cancela o reduce) -->
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <table class="table-small-text" style="width: 100%; margin: 0;">
                    <tr>
                        <td colspan="2" class="font-bold text-center" style="background-color: #C18579; color: #000; padding: 5px;">
                            Identificación del Proyecto en el que se cancela o reduce (4)
                        </td>
                    </tr>
                    <tr>
                        <td width="35%" class="text-left font-bold" style="padding: 3px;">Dependencia General:</td>
                        <td width="65%" class="text-left" style="padding: 3px;">{{ $reconduction->administrativeUnit->department->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold" style="padding: 3px;">Dependencia Auxiliar:</td>
                        <td class="text-left" style="padding: 3px;">{{ $reconduction->administrativeUnit->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold" style="padding: 3px;">Programa presupuestario:</td>
                        <td class="text-left" style="padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold" style="padding: 3px;">Objetivo:</td>
                        <td class="text-left" style="padding: 3px;"></td>
                    </tr>
                </table>
            </td>
            <!-- Espaciador -->
            <td style="width: 2%; border: none; padding: 0;"></td>
            <!-- Lado Derecho (Asigna o amplia) -->
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <table class="table-small-text" style="width: 100%; margin: 0;">
                    <tr>
                        <td colspan="2" class="font-bold text-center" style="background-color: #C18579; color: #000; padding: 5px;">
                            Identificación del Proyecto en el que se asigna o se amplia (5)
                        </td>
                    </tr>
                    <tr>
                        <td width="35%" class="text-left font-bold" style="padding: 3px;">Dependencia General:</td>
                        <td width="65%" class="text-left" style="padding: 3px;">{{ $reconduction->administrativeUnit->department->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold" style="padding: 3px;">Dependencia Auxiliar:</td>
                        <td class="text-left" style="padding: 3px;">{{ $reconduction->administrativeUnit->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold" style="padding: 3px;">Programa presupuestario:</td>
                        <td class="text-left" style="padding: 3px;"></td>
                    </tr>
                    <tr>
                        <td class="text-left font-bold" style="padding: 3px;">Objetivo:</td>
                        <td class="text-left" style="padding: 3px;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @php
        // Separaciones de ítems según dictamen OSFEM (Opcional, pero imprimiremos en bloque si reduce o si amplia)
        $reductions = $reconduction->items->whereIn('modification_type', ['reduction', 'cancellation']);
        $increases = $reconduction->items->whereIn('modification_type', ['increase', 'creation']);
    @endphp

    <!-- RECURSOS (6) y (7) -->
    <table style="width: 100%; border: none; margin-top: 15px; table-layout: fixed;">
        <tr>
            <!-- Left Side Resources Table (6) -->
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <table class="table-small-text" style="width: 100%; margin: 0; text-align: center;">
                    <tr>
                        <td colspan="6" class="font-bold text-center" style="background-color: #C18579; color: #000; padding: 5px;">Identificación de Recursos a nivel de Proyecto que se cancelan o se reducen. (6)</td>
                    </tr>
                    <tr class="bg-osfem">
                        <td rowspan="2" style="width: 15%">Clave</td>
                        <td rowspan="2" style="width: 25%">Denominación</td>
                        <td colspan="4">Presupuesto</td>
                    </tr>
                    <tr class="bg-osfem">
                        <td style="width: 15%">Autorizado</td>
                        <td style="width: 15%">Por ejercer</td>
                        <td style="width: 15%">Por cancelar o reducir</td>
                        <td style="width: 15%">Autorizado Modificado</td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                </table>
            </td>
            <!-- Spacer -->
            <td style="width: 2%; border: none; padding: 0;"></td>
            <!-- Right Side Resources Table (7) -->
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <table class="table-small-text" style="width: 100%; margin: 0; text-align: center;">
                    <tr>
                        <td colspan="5" class="font-bold text-center" style="background-color: #C18579; color: #000; padding: 5px;">Identificación de Recursos a nivel de Proyecto que se amplían o se asignan. (7)</td>
                    </tr>
                    <tr class="bg-osfem">
                        <td rowspan="2" style="width: 15%">Clave</td>
                        <td rowspan="2" style="width: 25%">Denominación</td>
                        <td colspan="3">Presupuesto</td>
                    </tr>
                    <tr class="bg-osfem">
                        <td style="width: 20%">Autorizado</td>
                        <td style="width: 20%">Ampliación y/o Reasignación</td>
                        <td style="width: 20%">Autorizado Modificado</td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td><td></td><td></td><td></td><td></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- METAS DE ACTIVIDAD (8) y (9) -->
    <table style="width: 100%; border: none; margin-top: 15px; table-layout: fixed;">
        <tr>
            <!-- Left Side Activities Table (8) -->
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <div class="font-bold text-center" style="margin-bottom: 5px; font-size: 9px;">Metas de Actividad Programadas y alcanzadas del Proyecto a cancelar o Reducir. (8)</div>
                <table class="table-small-text" style="width: 100%; margin: 0; text-align: center;">
                    <tr class="bg-osfem">
                        <td rowspan="2" style="width: 12%">Código</td>
                        <td rowspan="2" style="width: 24%">Descripción</td>
                        <td rowspan="2" style="width: 10%">Unidad de Medida</td>
                        <td colspan="3">Cantidad Programada de la Meta de Actividad</td>
                        <td colspan="4">Calendarización Trimestral Modificada</td>
                    </tr>
                    <tr class="bg-osfem">
                        <td style="width: 6%">Inicial</td>
                        <td style="width: 6%">Avance</td>
                        <td style="width: 6%">Modificada</td>
                        <td style="width: 4%">1</td>
                        <td style="width: 4%">2</td>
                        <td style="width: 4%">3</td>
                        <td style="width: 4%">4</td>
                    </tr>
                    @forelse($reductions as $item)
                    @php
                        $q1 = ($item->new_schedule['jan'] ?? 0) + ($item->new_schedule['feb'] ?? 0) + ($item->new_schedule['mar'] ?? 0);
                        $q2 = ($item->new_schedule['apr'] ?? 0) + ($item->new_schedule['may'] ?? 0) + ($item->new_schedule['jun'] ?? 0);
                        $q3 = ($item->new_schedule['jul'] ?? 0) + ($item->new_schedule['aug'] ?? 0) + ($item->new_schedule['sep'] ?? 0);
                        $q4 = ($item->new_schedule['oct'] ?? 0) + ($item->new_schedule['nov'] ?? 0) + ($item->new_schedule['dec'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $item->activity->id }}</td>
                        <td class="text-left" style="font-size: 7px;">{{ \Illuminate\Support\Str::limit($item->activity->name, 60) }}</td>
                        <td style="font-size: 7px;">{{ $item->activity->measurement_unit ?? 'Unidad' }}</td>
                        <td>{{ number_format($item->previous_annual_goal, 0) }}</td>
                        <td>{{ number_format($item->achieved_so_far, 0) }}</td>
                        <td class="font-bold">{{ number_format($item->new_annual_goal, 0) }}</td>
                        <td>{{ number_format($q1, 0) }}</td>
                        <td>{{ number_format($q2, 0) }}</td>
                        <td>{{ number_format($q3, 0) }}</td>
                        <td>{{ number_format($q4, 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="height: 15px;"></td>
                    </tr>
                    @endforelse
                </table>
            </td>
            <!-- Spacer -->
            <td style="width: 2%; border: none; padding: 0;"></td>
            <!-- Right Side Activities Table (9) -->
            <td style="width: 49%; vertical-align: top; border: none; padding: 0;">
                <div class="font-bold text-center" style="margin-bottom: 5px; font-size: 9px;">Metas de Actividad Programadas y alcanzadas del Proyecto que se crea o incrementa. (9)</div>
                <table class="table-small-text" style="width: 100%; margin: 0; text-align: center;">
                    <tr class="bg-osfem">
                        <td rowspan="2" style="width: 12%">Código</td>
                        <td rowspan="2" style="width: 24%">Descripción</td>
                        <td rowspan="2" style="width: 10%">Unidad de Medida</td>
                        <td colspan="3">Cantidad Programada de la Meta de Actividad</td>
                        <td colspan="4">Calendarización Trimestral Modificada</td>
                    </tr>
                    <tr class="bg-osfem">
                        <td style="width: 6%">Inicial</td>
                        <td style="width: 6%">Avance</td>
                        <td style="width: 6%">Modificada</td>
                        <td style="width: 4%">1</td>
                        <td style="width: 4%">2</td>
                        <td style="width: 4%">3</td>
                        <td style="width: 4%">4</td>
                    </tr>
                    @forelse($increases as $item)
                    @php
                        $q1 = ($item->new_schedule['jan'] ?? 0) + ($item->new_schedule['feb'] ?? 0) + ($item->new_schedule['mar'] ?? 0);
                        $q2 = ($item->new_schedule['apr'] ?? 0) + ($item->new_schedule['may'] ?? 0) + ($item->new_schedule['jun'] ?? 0);
                        $q3 = ($item->new_schedule['jul'] ?? 0) + ($item->new_schedule['aug'] ?? 0) + ($item->new_schedule['sep'] ?? 0);
                        $q4 = ($item->new_schedule['oct'] ?? 0) + ($item->new_schedule['nov'] ?? 0) + ($item->new_schedule['dec'] ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $item->activity->id }}</td>
                        <td class="text-left" style="font-size: 7px;">{{ \Illuminate\Support\Str::limit($item->activity->name, 60) }}</td>
                        <td style="font-size: 7px;">{{ $item->activity->measurement_unit ?? 'Unidad' }}</td>
                        <td>{{ number_format($item->previous_annual_goal, 0) }}</td>
                        <td>{{ number_format($item->achieved_so_far, 0) }}</td>
                        <td class="font-bold">{{ number_format($item->new_annual_goal, 0) }}</td>
                        <td>{{ number_format($q1, 0) }}</td>
                        <td>{{ number_format($q2, 0) }}</td>
                        <td>{{ number_format($q3, 0) }}</td>
                        <td>{{ number_format($q4, 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="height: 15px;"></td>
                    </tr>
                    @endforelse
                </table>
            </td>
        </tr>
    </table>

    @php
        $redJustifications = collect($reductions)->pluck('justification')->filter()->join(' | ');
        $incJustifications = collect($increases)->pluck('justification')->filter()->join(' | ');
    @endphp

    <!-- Justificación -->
    <div style="margin-top: 15px;">
        <table style="width: 30%; border-collapse: collapse;">
            <tr>
                <td class="font-bold" style="background-color: #C18579; color: #000; padding: 3px 5px; font-size: 10px; border: 1px solid #000;">
                    Justificación (10)
                </td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
            <tr>
                <td style="border: 1px solid #000; padding: 2px 5px; font-size: 8px; text-align: left; height: 25px; vertical-align: top;">
                    De la cancelación o reducción de metas de actividad y/o recursos del Proyecto. (impacto o repercusión programática) En su caso utilizar hoja anexa.<br>
                    <span style="font-size: 9px; margin-top: 3px; display: block;">{{ $redJustifications }}</span>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 2px 5px; font-size: 8px; text-align: left; height: 25px; vertical-align: top;">
                    De creación o reasignación de metas de actividad y/o recursos al proyecto (Beneficio, Impacto, Repercusión programática). En su caso utilizar hoja anexa.<br>
                    <span style="font-size: 9px; margin-top: 3px; display: block;">{{ $incJustifications }}</span>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000; padding: 2px 5px; font-size: 8px; text-align: left; height: 20px; vertical-align: top;">
                    Identificación del Origen de los recursos. En su caso utilizar hoja anexa.<br>
                </td>
            </tr>
        </table>
    </div>

    <!-- Signatures Panel -->
    <table style="width: 100%; margin-top: 15px; border: none; table-layout: fixed;">
        <tr>
            <!-- Elabora -->
            <td style="width: 30%; vertical-align: top; border: none; padding: 0;">
                <table style="width: 100%; text-align: center; border-collapse: collapse;">
                    <tr><td style="border: 1px solid #000; font-size: 8px; font-weight: bold; padding: 2px;">Elabora (Dep. General)</td></tr>
                    <tr><td style="border-left: 1px solid #000; border-right: 1px solid #000; height: 50px; vertical-align: bottom;">
                        {{ $reconduction->requestedBy->name ?? 'Enlace PBR' }}
                    </td></tr>
                    <tr><td style="border: 1px solid #000; font-size: 8px; font-weight: bold; padding: 2px;">Nombre y Firma</td></tr>
                </table>
            </td>
            <td style="width: 5%; border: none;"></td>
            <!-- VoBo -->
            <td style="width: 30%; vertical-align: top; border: none; padding: 0;">
                <table style="width: 100%; text-align: center; border-collapse: collapse;">
                    <tr><td style="border: 1px solid #000; font-size: 8px; font-weight: bold; padding: 2px;">Vo. Bo. (Tesorería)</td></tr>
                    <tr><td style="border-left: 1px solid #000; border-right: 1px solid #000; height: 50px; vertical-align: bottom;">
                        
                    </td></tr>
                    <tr><td style="border: 1px solid #000; font-size: 8px; font-weight: bold; padding: 2px;">Nombre y Firma</td></tr>
                </table>
            </td>
            <td style="width: 5%; border: none;"></td>
            <!-- Autorizo -->
            <td style="width: 30%; vertical-align: top; border: none; padding: 0;">
                <table style="width: 100%; text-align: center; border-collapse: collapse;">
                    <tr><td style="border: 1px solid #000; font-size: 8px; font-weight: bold; padding: 2px;">Autorizó (Titular de UIPPE o equivalente)</td></tr>
                    <tr><td style="border-left: 1px solid #000; border-right: 1px solid #000; height: 50px; vertical-align: bottom;">
                        {{ $reconduction->validatedBy->name ?? 'Órgano Fiscalizador' }}
                    </td></tr>
                    <tr><td style="border: 1px solid #000; font-size: 8px; font-weight: bold; padding: 2px;">Nombre y Firma</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Base Legal Footer -->
    <div style="font-size: 8px; margin-top:10px; text-transform:uppercase; color:#555;">
        CUANDO LAS ADECUACIONES APLIQUEN PARA MODIFICAR PRESUPUESTO, ESTAS SE DEBEN DEFINIR A NIVEL DE PARTIDA PRESUPUESTARIA Y CAPÍTULO DE GASTO EN RELACIÓN ANEXA. ESTO NO APLICA PARA ADECUACIONES PROGRAMÁTICAS, ES DECIR PARA MODIFICACIÓN DE PROGRAMACIÓN DE METAS DE ACTIVIDAD.
    </div>

</body>
</html>
