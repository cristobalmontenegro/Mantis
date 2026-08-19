<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('form_api.php');
require_api('gpc_api.php');
require_api('session_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

form_security_validate('plugin_totp_switch_state');

$t_user_id = auth_get_current_user_id();
$t_user_id = (int)$t_user_id;
$action = gpc_get_string('action', '');

if ($action === 'enable') {
    $secret = Totp::GenerateSecret(32);
    $encoded = Base32::encode($secret);

    session_set('totp_pending_secret', $encoded);

    form_security_purge('plugin_totp_switch_state');
    print_header_redirect(plugin_page('verify-totp', true));
    exit;
}

if ($action === 'disable') {
    if (isUserTOTPConfigured($t_user_id)) {
        db_query(
            "DELETE FROM " . plugin_table("totp") . " WHERE user_id = " . db_param() . " LIMIT 1",
            array($t_user_id)
        );
    }

    form_security_purge('plugin_totp_switch_state');
    print_header_redirect(plugin_page('manage-totp', true));
    exit;
}

form_security_purge('plugin_totp_switch_state');
print_header_redirect(plugin_page('manage-totp', true));
