<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Constancia RAMT</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: {{ $config->primary_color ?? '#6366f1' }};
            --primary-light: {{ ($config->primary_color ?? '#6366f1') . '10' }};
            --success: #10b981;
            --success-light: #10b98115;
            --dark: #0f172a;
            --gray-light: #f8fafc;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.12);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-header {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0) 100%);
            padding: 40px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }

        .logo {
            max-height: 60px;
            max-width: 110px;
            object-fit: contain;
        }

        .municipio-name {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }

        .admin-period {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }

        .card-body {
            padding: 40px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--success-light);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: var(--success);
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 30px;
            animation: pulse 2.5s infinite;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 35px;
            line-height: 1.3;
            text-align: center;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
            margin: 40px 0 20px 0;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-row {
            background: var(--gray-light);
            border: 1px solid var(--border);
            padding: 18px;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .info-value.folio {
            font-family: monospace;
            font-size: 0.85rem;
            word-break: break-all;
            color: #334155;
        }

        /* Area / Activities Styles */
        .area-block {
            margin-bottom: 35px;
        }

        .area-header {
            background: var(--primary-light);
            color: var(--primary);
            padding: 14px 20px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            border-left: 5px solid var(--primary);
        }

        .activity-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .activity-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .activity-code {
            font-size: 0.75rem;
            background: #f1f5f9;
            color: #475569;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-family: monospace;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .progress-bar {
            height: 100%;
            background: var(--success);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .monthly-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .monthly-item {
            border: 1px solid #f1f5f9;
            background: #fafafb;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .month-name {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .month-values {
            color: #475569;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .evidence-thumb-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .evidence-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .evidence-thumb:hover {
            transform: scale(1.1);
        }

        .no-evidence {
            font-size: 0.75rem;
            color: #94a3b8;
            font-style: italic;
        }

        .footer {
            background: var(--gray-light);
            padding: 25px 40px;
            text-align: center;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            color: #64748b;
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @media (max-width: 640px) {
            .card-body {
                padding: 20px;
            }
            .card-header {
                padding: 30px 20px;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="logo-container">
                @if($config->logo_path)
                    <img src="{{ tenant_asset($config->logo_path) }}" class="logo" alt="Logo">
                @endif
                @if($config->shield_path)
                    <img src="{{ tenant_asset($config->shield_path) }}" class="logo" alt="Escudo">
                @endif
            </div>
            <div class="municipio-name">{{ $config->official_name ?? 'H. Ayuntamiento' }}</div>
            <div class="admin-period">Administración {{ $config->administration_period ?? 'Actual' }}</div>
        </div>
        
        <div class="card-body">
            <div style="text-align: center;">
                <div class="status-badge">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="display:inline-block; vertical-align:middle; margin-right: 5px;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Certificado Oficial Válido
                </div>
            </div>

            <div class="title">Constancia de Cumplimiento Trimestral (RAMT)</div>

            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Dependencia General</div>
                    <div class="info-value">{{ mb_strtoupper($department->name) }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Trimestre Acreditado</div>
                    <div class="info-value">Trimestre {{ $certificate->quarter }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Fecha de Expedición</div>
                    <div class="info-value">{{ $certificate->issued_at->format('d/m/Y H:i:s') }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Folio Digital (UUID)</div>
                    <div class="info-value folio">{{ $certificate->certificate_folio }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Firma de Seguridad Auditora</div>
                    <div class="info-value folio">{{ sha1($certificate->certificate_folio . $certificate->department_id) }}</div>
                </div>
            </div>

            <div class="section-title">Reporte Detallado de Avances y Evidencias</div>

            @foreach($department->administrativeUnits as $area)
                @if($area->substantiveActivities->count() > 0)
                    <div class="area-block">
                        <div class="area-header">
                            Área: {{ $area->name }} (Cve: {{ $area->general_sector_code }}-{{ $area->auxiliary_sector_code }})
                        </div>

                        @foreach($area->substantiveActivities as $activity)
                            <div class="activity-card">
                                <div class="activity-header">
                                    <div class="activity-title">{{ $activity->name }}</div>
                                    <div class="activity-code">{{ $activity->code ?? 'N/A' }}</div>
                                </div>

                                <div>
                                    <span style="font-size: 0.85rem; font-weight: 600; color: #475569;">Avance Global del Trimestre:</span>
                                    <span style="font-size: 0.85rem; font-weight: 800; color: var(--success); float: right;">
                                        {{ $activity->trimestral_compliance_percent }}%
                                    </span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: {{ $activity->trimestral_compliance_percent }}%;"></div>
                                </div>

                                <div class="monthly-grid">
                                    @foreach($monthsArr as $m)
                                        @php
                                            $report = $activity->progressReports->where('month', $m)->first();
                                        @endphp
                                        <div class="monthly-item">
                                            <div class="month-name">{{ $monthNames[$m] }}</div>
                                            
                                            <div class="month-values">
                                                @if($activity->monthlySchedule)
                                                    @php
                                                        $col = [
                                                            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'may', 6 => 'jun',
                                                            7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dec'
                                                        ][$m] . '_programmed';
                                                        $programmed = (float) $activity->monthlySchedule->$col;
                                                    @endphp
                                                    <strong>Meta:</strong> {{ $programmed }}<br>
                                                @endif
                                                <strong>Reportado:</strong> {{ $report ? $report->reported_value : '0' }}<br>
                                                <strong>Estatus:</strong> <span style="color: var(--success); font-weight: 700;">Validado</span>
                                            </div>

                                            @if($report && $report->evidence_url)
                                                <div class="evidence-thumb-container">
                                                    <a href="{{ $report->evidence_url }}" target="_blank" title="Ver evidencia original">
                                                        <img src="{{ $report->evidence_url }}" class="evidence-thumb" alt="Evidencia">
                                                    </a>
                                                    <span style="font-size: 0.7rem; color: #64748b;">Evidencia</span>
                                                </div>
                                            @else
                                                <div class="no-evidence">Sin archivo de evidencia</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach

        </div>

        <div class="footer">
            Sistema Presupuesto Basado en Resultados (PbR) | Municipio de {{ $config->official_name ?? 'H. Ayuntamiento' }} | <a href="/dashboard">Ir al Cockpit Principal</a>
        </div>
    </div>
</div>

</body>
</html>
