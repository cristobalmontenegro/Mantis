<?php
require_once('core.php');
require_api('authentication_api.php');
require_api('html_api.php');
require_api('lang_api.php');
require_api('print_api.php');

auth_ensure_user_authenticated();
auth_reauthenticate();
current_user_ensure_admin();

layout_page_header(plugin_lang_get('title'));
layout_page_begin();
print_admin_menu('manage_plugin_page.php');

$t_issuer = plugin_config_get('issuer');
?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>

    <div class="form-container">
        <h3><?php echo plugin_lang_get('title'); ?></h3>
        <p><?php echo plugin_lang_get('description'); ?></p>

        <form action="<?php echo plugin_page('config_update'); ?>" method="post">
            <?php echo form_security_field('plugin_totp_config_update'); ?>

            <div class="form-group">
                <label for="issuer"><?php echo plugin_lang_get('config_issuer'); ?></label>
                <input type="text" name="issuer" id="issuer"
                       class="form-control"
                       value="<?php echo string_attribute($t_issuer); ?>"
                       style="max-width:400px" />
                <p class="help-block"><?php echo plugin_lang_get('config_issuer_help'); ?></p>
            </div>

            <input type="submit" class="btn btn-primary btn-sm"
                   value="<?php echo lang_get('config_update_button'); ?>" />
        </form>
    </div>
</div>

<?php
layout_page_end();
