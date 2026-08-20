<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('session_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();

$t_user_id = auth_get_current_user_id();
$t_user_id = (int)$t_user_id;

if (!isUserTOTPConfigured($t_user_id)) {
    print_header_redirect(plugin_page('manage-totp', true));
    exit;
}

$t_username = current_user_get_field('username');
$t_issuer = plugin_config_get('issuer');

// Check if we have codes in session (just generated)
$t_codes = session_get('totp_backup_codes');
if ($t_codes === null || !is_array($t_codes) || count($t_codes) === 0) {
    // No codes available - redirect back
    print_header_redirect(plugin_page('manage-totp', true));
    exit;
}

// Clear codes from session after displaying
session_set('totp_backup_codes', null);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo plugin_lang_get('totp_backup_codes_title'); ?> - <?php echo $t_issuer; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            color: #333;
            max-width: 700px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header .subtitle {
            font-size: 14px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .warning strong {
            color: #856404;
        }
        .codes-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 25px;
        }
        .code-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px 15px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 2px;
        }
        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #0275d8;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-print:hover {
            background: #025aa5;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
            .warning { background: #fff; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>

    <div class="header">
        <h1>Códigos de Respaldo TOTP</h1>
        <div class="subtitle"><?php echo $t_issuer; ?> - <?php echo $t_username; ?></div>
    </div>

    <div class="warning">
        <strong>Guarda estos códigos en un lugar seguro.</strong><br>
        Cada código solo se puede usar una vez. Si pierdes acceso a tu aplicación de autenticación,
        podrás usar estos códigos para iniciar sesión.
    </div>

    <div class="codes-grid">
        <?php foreach ($t_codes as $t_code) { ?>
            <div class="code-item"><?php echo $t_code; ?></div>
        <?php } ?>
    </div>

    <div class="footer">
        Generado el <?php echo date('d/m/Y H:i'); ?> - <?php echo $t_issuer; ?>
    </div>

</body>
</html>
