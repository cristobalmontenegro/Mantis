<?php

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_unprotected();

$t_user_id = auth_get_current_user_id();

$isTOTPConfigured = isUserTOTPConfigured($t_user_id);
$t_backup_codes_remaining = 0;

if ($isTOTPConfigured) {
    $t_backup_codes_remaining = getBackupCodesRemaining($t_user_id);
}

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
                <p><?php echo plugin_lang_get("totp_enabled_info"); ?></p>

                <?php
                // Show backup codes after first enable or regeneration
                $t_session_codes = session_get('totp_backup_codes');
                if ($t_session_codes !== null && is_array($t_session_codes) && count($t_session_codes) > 0) {
                ?>
                    <div class="alert alert-info">
                        <h4><?php echo plugin_lang_get('totp_backup_codes_title'); ?></h4>
                        <p><?php echo plugin_lang_get('totp_backup_codes_warning'); ?></p>
                        <div style="background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 14px;">
                            <?php foreach ($t_session_codes as $t_code) { ?>
                                <div><?php echo $t_code; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php session_set('totp_backup_codes', null); ?>
                <?php } ?>

                <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <tr>
                        <th class="category" style="width:40%"><?php echo plugin_lang_get('totp_backup_codes_remaining'); ?></th>
                        <td>
                            <span class="label <?php echo $t_backup_codes_remaining > 3 ? 'label-success' : ($t_backup_codes_remaining > 0 ? 'label-warning' : 'label-danger'); ?>">
                                <?php echo $t_backup_codes_remaining; ?>
                            </span>
                            <?php if ($t_backup_codes_remaining <= 3) { ?>
                                <small class="text-muted"> - <?php echo plugin_lang_get('totp_backup_codes_low'); ?></small>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="category">Acciones</th>
                        <td>
                            <form action="<?php echo plugin_page('manage-totp'); ?>" method="post" style="display:inline; margin-right:10px;">
                                <?php echo form_security_field('plugin_totp_regenerate_codes'); ?>
                                <input type="hidden" name="action" value="regenerate_codes" />
                                <input type="submit" value="<?php echo plugin_lang_get('totp_regenerate_codes'); ?>"
                                       class="btn btn-warning btn-sm"
                                       onclick="return confirm('<?php echo addslashes(plugin_lang_get('totp_regenerate_codes_confirm')); ?>')" />
                            </form>

                            <form action="<?php echo plugin_page('switch-totp-state'); ?>" method="post" style="display:inline;">
                                <?php echo form_security_field('plugin_totp_switch_state'); ?>
                                <input type="hidden" name="action" value="disable" />
                                <input type="submit" value="<?php echo plugin_lang_get('totp_disable_button'); ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('<?php echo addslashes(plugin_lang_get('totp_disable_confirm')); ?>')" />
                            </form>
                        </td>
                    </tr>
                </table>
                </div>

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

// Handle regenerate codes action
if ($isTOTPConfigured && isset($_POST['action']) && $_POST['action'] === 'regenerate_codes') {
    form_security_validate('plugin_totp_regenerate_codes');
    $t_new_codes = generateBackupCodes($t_user_id);
    form_security_purge('plugin_totp_regenerate_codes');
}

layout_page_end();
