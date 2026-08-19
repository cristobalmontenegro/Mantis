<?php

require_once('core.php');
require_api('authentication_api.php');
require_api('html_api.php');
require_api('lang_api.php');
require_api('print_api.php');
require_api('user_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

layout_page_header(plugin_lang_get('manage'));
layout_page_begin();
print_account_menu('plugin.php?page=TOTP/manage-totp');

$t_user_id = auth_get_current_user_id();

$isTOTPConfigured = isUserTOTPConfigured($t_user_id);
?>

    <div class="col-md-12 col-xs-12">
        <div class="space-10">
        </div>

        <div id="user-custom-fields" class="form-container">
            <h3>
                <?php echo plugin_lang_get($isTOTPConfigured ? "totp_enabled" : "totp_not_enabled"); ?>
            </h3>

            <?php
            if ($isTOTPConfigured) {
                echo "<p><img src='" . plugin_page('render-qrcode') . "'/></p>";
                echo "<p><small>" . plugin_lang_get("totp_scan_qr") . "</small></p>";
            } else {
                echo "<p>" . plugin_lang_get("totp_setup_instructions") . "</p>";
            }
            ?>
            <br/>

            <form action="<?php echo plugin_page('switch-totp-state'); ?>" method="post">
                <?php echo form_security_field('plugin_totp_switch_state'); ?>
                <input type="submit"
                       value="<?php echo plugin_lang_get($isTOTPConfigured ? 'totp_disable_button' : 'totp_enable_button'); ?>"
                       class="button-totp <?php echo $isTOTPConfigured ? 'button-totp-enabled' : 'button-totp-disabled'; ?>"/>
            </form>
        </div>
    </div>

    <style>
        .button-totp {
            border: 0;
            border-radius: 4px;
            padding: 10px 20px;
            color: white;
            cursor: pointer;
        }
        .button-totp-enabled {
            background: #d9534f;
        }
        .button-totp-disabled {
            background: #5cb85c;
        }
    </style>

<?php
layout_page_end();
