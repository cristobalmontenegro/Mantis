<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('form_api.php');
require_api('gpc_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

form_security_validate('plugin_totp_switch_state');

$t_user_id = auth_get_current_user_id();
$t_user_id = (int)$t_user_id;

if (!isUserTOTPConfigured($t_user_id)) {
    $secret = Totp::GenerateSecret(32);
    $encoded = Base32::encode($secret);
    $query = "INSERT INTO " . plugin_table("totp") . " (user_id, secret_key) VALUES ($t_user_id, '" . db_quote($encoded) . "')";
} else {
    $query = "DELETE FROM " . plugin_table("totp") . " WHERE user_id = $t_user_id LIMIT 1";
}

db_query($query);

form_security_purge('plugin_totp_switch_state');

print_header_redirect(plugin_page('manage-totp', true));
