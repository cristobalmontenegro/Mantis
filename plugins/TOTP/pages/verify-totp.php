<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('form_api.php');
require_api('gpc_api.php');
require_api('html_api.php');
require_api('lang_api.php');
require_api('print_api.php');
require_api('session_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

$t_pending_secret = session_get('totp_pending_secret');

if ($t_pending_secret === null || $t_pending_secret === '') {
    print_header_redirect(plugin_page('manage-totp', true));
    exit;
}

$f_totp = gpc_get_string('totp_code', '');
$f_error = gpc_get_bool('error', false);
$t_user_id = auth_get_current_user_id();
$t_user_id = (int)$t_user_id;

if ($f_totp !== '') {
    $f_totp = trim($f_totp);

    if (!preg_match('/^\d{6}$/', $f_totp)) {
        print_header_redirect(plugin_page('verify-totp', true) . '&error=1');
        exit;
    }

    $t_totp = new Totp();
    $t_expected = $t_totp->GenerateToken(Base32::decode($t_pending_secret));

    if (hash_equals($t_expected, $f_totp)) {
        db_query(
            "INSERT INTO " . plugin_table("totp") . " (user_id, secret_key) VALUES (" . db_param() . ", " . db_param() . ")",
            array($t_user_id, $t_pending_secret)
        );

        session_set('totp_pending_secret', null);

        print_header_redirect(plugin_page('manage-totp', true));
        exit;
    } else {
        print_header_redirect(plugin_page('verify-totp', true) . '&error=1');
        exit;
    }
}

layout_page_header(plugin_lang_get('manage'));
layout_page_begin();
print_account_menu('plugin.php?page=TOTP/manage-totp');

$t_issuer = plugin_config_get('issuer');
$t_user_username = current_user_get_field('username');
$t_otpauth_url = "otpauth://totp/" . urlencode($t_issuer) . ":" . urlencode($t_user_username)
    . "?secret=" . $t_pending_secret
    . "&issuer=" . urlencode($t_issuer);
?>

    <div class="col-md-12 col-xs-12">
        <div class="space-10"></div>

        <div class="form-container">
            <h3><?php echo plugin_lang_get('totp_verify_title'); ?></h3>

            <?php if ($f_error) { ?>
                <div class="alert alert-danger">
                    <?php echo plugin_lang_get('totp_verify_error'); ?>
                </div>
            <?php } ?>

            <p><?php echo plugin_lang_get('totp_verify_instructions'); ?></p>

            <p><img src="<?php echo plugin_page('render-qrcode'); ?>" /></p>

            <p><small><?php echo plugin_lang_get('totp_scan_qr'); ?></small></p>

            <form action="<?php echo plugin_page('verify-totp'); ?>" method="post">
                <?php echo form_security_field('plugin_totp_verify'); ?>

                <div class="form-group">
                    <label for="totp_code"><?php echo plugin_lang_get('totp'); ?></label>
                    <input type="text" id="totp_code" name="totp_code"
                           class="form-control"
                           maxlength="6" pattern="\d{6}" inputmode="numeric"
                           autocomplete="one-time-code"
                           placeholder="123456"
                           style="max-width:200px; font-size:24px; letter-spacing:8px; text-align:center"
                           autofocus required />
                </div>

                <input type="submit" class="btn btn-success btn-sm"
                       value="<?php echo plugin_lang_get('totp_verify_button'); ?>" />

                <a href="<?php echo plugin_page('manage-totp'); ?>" class="btn btn-default btn-sm">
                    <?php echo lang_get('cancelled') ?>
                </a>
            </form>
        </div>
    </div>

<?php
layout_page_end();
