<?php

require_once('core.php');
require_api('authentication_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();

$t_user_id = auth_get_current_user_id();
$t_user_username = current_user_get_field("username");
$t_issuer = plugin_config_get('issuer');
$t_secret = retrieveTOTPForUser($t_user_id);

$t_otpauth_url = "otpauth://totp/" . urlencode($t_issuer) . ":" . urlencode($t_user_username)
    . "?secret=" . $t_secret
    . "&issuer=" . urlencode($t_issuer);

QRCode::png($t_otpauth_url, false, QR_ECLEVEL_L, 6);
