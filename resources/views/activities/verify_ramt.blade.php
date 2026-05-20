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
            --primary-light: {{ ($config->primary_color ?? '#6366f1') . '15' }};
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
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card-header {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.5) 0%, rgba(255, 255, 255, 0) 100%);
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .logo {
            max-height: 50px;
            max-width: 90px;
            object-fit: contain;
        }

        .municipio-name {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .admin-period {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .card-body {
            padding: 30px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--success-light);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success);
            padding: 12px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }

        .title {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 25px;
            line-height: 1.3;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-row {
            background: var(--gray-light);
            border: 1px solid var(--border);
            padding: 15px;
            border-radius: 16px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dark);
        }

        .info-value.folio {
            font-family: monospace;
            font-size: 0.85rem;
            word-break: break-all;
            color: #334155;
        }

        .footer {
            background: var(--gray-light);
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid var(--border);
            font-size: 0.75rem;
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

        @media (max-width: 480px) {
            .card-body {
                padding: 20px;
            }
            .card-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

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
        <div class="status-badge">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" style="display:inline-block; vertical-align:middle;">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            Certificado Válido y Homologado
        </div>

        <div class="title">Constancia de Cumplimiento Trimestral (RAMT)</div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Dependencia Evaluada</div>
                <div class="info-value">{{ mb_strtoupper($certificate->department->name) }}</div>
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
                <div class="info-label">Código de Firma Auditora</div>
                <div class="info-value folio">{{ sha1($certificate->certificate_folio . $certificate->department_id) }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        Sistema Presupuesto Basado en Resultados (PbR) | <a href="/dashboard">Ir al Cockpit Principal</a>
    </div>
</div>

</body>
</html>
