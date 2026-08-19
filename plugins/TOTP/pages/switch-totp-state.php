<?php

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

form_security_validate('plugin_totp_switch_state');

$t_user_id = auth_get_current_user_id();
$t_user_id = (int)$t_user_id;

if (!isUserTOTPConfigured($t_user_id)) {
    $secret = Totp::GenerateSecret(32);
    $encoded = Base32::encode($secret);
    db_query(
        "INSERT INTO " . plugin_table("totp") . " (user_id, secret_key) VALUES (" . db_param() . ", " . db_param() . ")",
        array($t_user_id, $encoded)
    );
} else {
    db_query(
        "DELETE FROM " . plugin_table("totp") . " WHERE user_id = " . db_param() . " LIMIT 1",
        array($t_user_id)
    );
}

form_security_purge('plugin_totp_switch_state');

print_header_redirect(plugin_page('manage-totp', true));
