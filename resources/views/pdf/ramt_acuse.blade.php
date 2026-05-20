<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Constancia RAMT</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            margin-bottom: 30px;
            border-bottom: 2px solid
                {{ $config->primary_color ?? '#333' }}
            ;
            padding-bottom: 10px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color:
                {{ $config->primary_color ?? '#333' }}
            ;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .content {
            line-height: 1.6;
            margin-bottom: 30px;
            text-align: justify;
        }

        .activities-table {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .activities-table th,
        .activities-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .activities-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .status-badge {
            color: #15803d;
            font-weight: bold;
        }

        .footer-signatures {
            margin-top: 60px;
        }

        .footer-line {
            border-top: 1px solid #333;
            width: 250px;
            margin: 0 auto;
            padding-top: 5px;
        }

        .logo-img {
            max-height: 80px;
            max-width: 120px;
            object-fit: contain;
        }
    </style>
</head>

<body>

    @php
        function getBase64Image($path)
        {
            if (!$path)
                return '';
            $fullPath = Illuminate\Support\Facades\Storage::disk('public')->path($path);
            if (file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return '';
        }
        $logoBase64 = getBase64Image($config->logo_path ?? '');
        $shieldBase64 = getBase64Image($config->shield_path ?? '');
    @endphp

    <table class="header-table">
        <tr>
            <td width="20%">
                @if($shieldBase64)
                    <img src="{{ $shieldBase64 }}" class="logo-img" alt="Escudo">
                @else
                    <span style="color: #999;">LOGO NO DISPONIBLE</span>
                @endif
            </td>
            <td width="60%" class="text-center">
                <div class="title">{{ $config->official_name ?? 'H. Ayuntamiento' }}</div>
                <div class="subtitle">Administración {{ $config->administration_period ?? 'Actual' }}</div>
                <div class="subtitle">Reporte de Avance de Metas Trimestral (RAMT)</div>
            </td>
            <td width="20%" class="text-right">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                @endif
            </td>
        </tr>
    </table>

    <div class="text-right" style="margin-bottom: 20px;">
        <strong>Fecha de expedición:</strong> {{ date('d/m/Y') }}<br>
        <strong>Trimestre Evaluado:</strong> {{ $quarter }}
    </div>

    <div class="content">
        <strong>Reporte de Avance de Metas Trimestral "RAMT"</strong>, validando así el desempeñoy cumplimiento de las
        metas de Presupuesto Basado en Resultados
        Municipal (PbRM) correspondiente al <strong>Trimestre {{ $quarter }}</strong> para todas las variables y áreas
        asignadas a la Dependencia General:
        <strong>{{ mb_strtoupper($department->name) }} </strong>
    </div>

    @foreach($department->administrativeUnits as $area)
        @if($area->substantiveActivities->count() > 0)
            <div
                style="background-color: #f3f4f6; color: #374151; padding: 6px 10px; font-weight: bold; font-size: 11px; border-left: 4px solid {{ $config->primary_color ?? '#6366f1' }}; margin-top: 15px;">
                ÁREA DE RESPONSABILIDAD: {{ mb_strtoupper($area->name) }} (CVE:
                {{ $area->general_sector_code }}-{{ $area->auxiliary_sector_code }})
            </div>
            <table class="activities-table" style="margin-top: 0px; margin-bottom: 25px;">
                <thead>
                    <tr>
                        <th width="60%">NOMBRE DE LA ACTIVIDAD SUSTANTIVA</th>
                        <th width="25%">ESTATUS TRIMESTRAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($area->substantiveActivities as $act)
                        <tr>
                            <td>{{ $act->name }}</td>
                            <td class="text-center status-badge">Avance {{ $act->trimestral_compliance_percent ?? '0' }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <table class="footer-signatures text-center" style="width: 100%; table-layout: fixed; margin-top: 50px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                @if($department->holder)
                    <br><br><br>
                    <div class="footer-line" style="width: 80%;">
                        <strong>{{ mb_strtoupper($department->holder->academic_degree ?? '') }}
                            {{ mb_strtoupper($department->holder->first_name) }}
                            {{ mb_strtoupper($department->holder->last_name) }}</strong><br>
                        {{ mb_strtoupper($department->holder->position_title) }}
                    </div>
                @else
                    <br><br><br>
                    <div class="footer-line" style="width: 80%;">
                        <strong>TITULAR PENDIENTE DE ASIGNACIÓN</strong><br>
                        TITULAR DE LA DEPENDENCIA
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- SELLO DE TRAZABILIDAD - ÓRGANO AUDITOR -->
    <div
        style="margin-top: 50px; border-top: 1px dashed {{ $config->primary_color ?? '#333' }}; padding-top: 20px; page-break-inside: avoid;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%; vertical-align: top; line-height: 1.4;">
                    <h4
                        style="margin: 0 0 8px 0; color: {{ $config->primary_color ?? '#333' }}; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">
                        TRAZABILIDAD
                    </h4>
                    <div style="font-family: monospace; font-size: 9px; color: #555;">
                        <strong>FOLIO DE CERTIFICACIÓN (UUID):</strong> {{ $certificate_folio }}<br>
                        <strong>FECHA DE REGISTRO / EMISIÓN:</strong> {{ date('Y-m-d H:i:s') }}<br>
                        <strong>URL AUDITORÍA PBR:</strong> <span
                            style="color: {{ $config->primary_color ?? '#6366f1' }};">{{ $link_auditoria }}</span>
                    </div>
                </td>
                <td style="width: 25%; text-align: right; vertical-align: middle;">
                    <img src="data:image/svg+xml;base64,{{ $qr_code_base64 }}" style="width: 100px; height: 100px;"
                        alt="QR Verification" />
                </td>
            </tr>
        </table>
    </div>

</body>

</html>