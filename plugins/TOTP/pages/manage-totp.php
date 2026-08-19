<?php

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

$t_user_id = auth_get_current_user_id();

$isTOTPConfigured = isUserTOTPConfigured($t_user_id);

layout_page_header(plugin_lang_get('manage'));
layout_page_begin();
print_account_menu('plugin.php?page=TOTP/manage-totp');
?>

    <div class="col-md-12 col-xs-12">
        <div class="space-10"></div>

        <div id="user-custom-fields" class="form-container">
            <h3>
                <?php echo plugin_lang_get($isTOTPConfigured ? "totp_enabled" : "totp_not_enabled"); ?>
            </h3>

            <?php if ($isTOTPConfigured) { ?>
                <p><img src="<?php echo plugin_page('render-qrcode'); ?>" /></p>
                <p><small><?php echo plugin_lang_get("totp_scan_qr"); ?></small></p>

                <form action="<?php echo plugin_page('switch-totp-state'); ?>" method="post">
                    <?php echo form_security_field('plugin_totp_switch_state'); ?>
                    <input type="hidden" name="action" value="disable" />
                    <input type="submit"
                           value="<?php echo plugin_lang_get('totp_disable_button'); ?>"
                           class="button-totp button-totp-enabled"/>
                </form>
            <?php } else { ?>
                <p><?php echo plugin_lang_get("totp_setup_instructions"); ?></p>

                <form action="<?php echo plugin_page('switch-totp-state'); ?>" method="post">
                    <?php echo form_security_field('plugin_totp_switch_state'); ?>
                    <input type="hidden" name="action" value="enable" />
                    <input type="submit"
                           value="<?php echo plugin_lang_get('totp_enable_button'); ?>"
                           class="button-totp button-totp-disabled"/>
                </form>
            <?php } ?>
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
