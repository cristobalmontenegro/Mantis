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

        // Clean up related data
        db_query(
            "DELETE FROM " . plugin_table("backup_codes") . " WHERE user_id = " . db_param(),
            array($t_user_id)
        );
        db_query(
            "DELETE FROM " . plugin_table("failed_attempts") . " WHERE user_id = " . db_param(),
            array($t_user_id)
        );
        db_query(
            "DELETE FROM " . plugin_table("remembered_devices") . " WHERE user_id = " . db_param(),
            array($t_user_id)
        );

        // Clear remember cookie
        $t_cookie_name = config_get_global('cookie_prefix') . '_totp_remember_' . $t_user_id;
        gpc_set_cookie($t_cookie_name, '', time() - 3600, '/');
    }

    form_security_purge('plugin_totp_switch_state');
    print_header_redirect(plugin_page('manage-totp', true));
    exit;
}

form_security_purge('plugin_totp_switch_state');
print_header_redirect(plugin_page('manage-totp', true));
