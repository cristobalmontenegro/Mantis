<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('config_api.php');
require_api('constant_inc.php');
require_api('gpc_api.php');
require_api('print_api.php');
require_api('session_api.php');
require_api('string_api.php');

$f_username = gpc_get_string('username', '');
$f_password = gpc_get_string('password', '');
$f_totp = gpc_get_string('totp', '');
$t_return = string_url(string_sanitize_url(gpc_get_string('return', config_get_global('default_home_page'))));
$f_from = gpc_get_string('from', '');
$f_secure_session = gpc_get_bool('secure_session', false);
$f_reauthenticate = gpc_get_bool('reauthenticate', false);
$f_install = gpc_get_bool('install');

if ($f_install) {
    $t_return = 'admin/install.php';
}

$f_username = auth_prepare_username($f_username);
$f_password = auth_prepare_password($f_password);

$t_user_id = auth_get_user_id_from_login_name($f_username);
$t_allow_perm_login = auth_allow_perm_login($t_user_id, $f_username);
$f_perm_login = $t_allow_perm_login && gpc_get_bool('perm_login');

gpc_set_cookie(config_get_global('cookie_prefix') . '_secure_session', $f_secure_session ? '1' : '0');

$f_totp = trim($f_totp);
if (!preg_match('/^\d{6}$/', $f_totp)) {
    $f_totp = '';
}

if (auth_does_password_match($t_user_id, $f_password)) {
    $secret_key = retrieveTOTPForUser($t_user_id);

    if ($secret_key !== '') {
        $t_valid = false;
        $t_current_time = time();
        $t_step = 30;
        $t_totp = new Totp();

        for ($t_offset = -1; $t_offset <= 1; $t_offset++) {
            $t_time = $t_current_time + ($t_offset * $t_step);
            $t_expected = $t_totp->GenerateToken(Base32::decode($secret_key), $t_time);

            if (hash_equals($t_expected, $f_totp)) {
                $t_valid = true;
                break;
            }
        }

        if ($t_valid) {
            auth_attempt_login($f_username, $f_password, $f_perm_login);
            session_set('secure_session', $f_secure_session);

            $t_redirect_url = 'login_cookie_test.php?return=' . $t_return;
            print_header_redirect($t_redirect_url);
            exit;
        }
    }
}

user_increment_failed_login_count($t_user_id);

$t_query_args = array(
    'error' => 1,
    'username' => $f_username,
    'return' => $t_return,
);

if ($f_reauthenticate) {
    $t_query_args['reauthenticate'] = 1;
}

if ($f_secure_session) {
    $t_query_args['secure_session'] = 1;
}

if ($t_allow_perm_login && $f_perm_login) {
    $t_query_args['perm_login'] = 1;
}

$t_query_text = http_build_query($t_query_args, '', '&');

$t_redirect_url = auth_login_page($t_query_text);

if (HTTP_AUTH == config_get_global('login_method')) {
    auth_http_prompt();
    exit;
}

print_header_redirect($t_redirect_url);
