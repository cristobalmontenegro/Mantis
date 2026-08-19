<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('form_api.php');
require_api('gpc_api.php');
require_api('access_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();
access_ensure_global_level(ADMINISTRATOR);

form_security_validate('plugin_totp_admin_disable');

$t_user_id = (int)gpc_get_int('user_id', 0);

if ($t_user_id > 0 && isUserTOTPConfigured($t_user_id)) {
    db_query(
        "DELETE FROM " . plugin_table("totp") . " WHERE user_id = " . db_param() . " LIMIT 1",
        array($t_user_id)
    );
}

form_security_purge('plugin_totp_admin_disable');

print_header_redirect('manage_user_page.php?id=' . $t_user_id);
