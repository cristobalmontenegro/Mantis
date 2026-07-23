<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

layout_page_header( plugin_lang_get( 'title' ) );
layout_page_begin( 'config.php' );
print_manage_menu();

$t_current_threshold = (int)plugin_config_get( 'readonly_threshold' );

$t_access_levels = array(
    VIEWER   => 'VIEWER (' . VIEWER . ')',
    REPORTER => 'REPORTER (' . REPORTER . ')',
    UPDATER  => 'UPDATER (' . UPDATER . ')',
    DEVELOPER => 'DEVELOPER (' . DEVELOPER . ')',
    MANAGER  => 'MANAGER (' . MANAGER . ')',
);
?>
<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container">
<br>
<form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
<?php echo form_security_field( 'plugin_ResolutionAccess_config_edit' ) ?>
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
    <h4 class="widget-title lighter">
        <i class="ace-icon fa fa-lock"></i>
        <?php echo plugin_lang_get( 'title' ) ?>
    </h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive">
<table class="table table-bordered table-condensed table-striped">
<tr>
    <td class="category" width="50%">
        <?php echo plugin_lang_get( 'readonly_threshold_label' ) ?>
    </td>
    <td width="50%">
        <select name="readonly_threshold" class="input-sm">
<?php foreach ( $t_access_levels as $t_level => $t_label ): ?>
            <option value="<?php echo $t_level ?>" <?php echo ( $t_current_threshold == $t_level ) ? 'selected="selected"' : '' ?>><?php echo $t_label ?></option>
<?php endforeach; ?>
        </select>
        <p class="help-block"><?php echo plugin_lang_get( 'readonly_threshold_desc' ) ?></p>
    </td>
</tr>
</table>
</div>
</div>
<div class="widget-toolbox padding-8 clearfix">
    <input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'change_configuration' ) ?>" />
</div>
</div>
</div>
</form>
</div>
</div>
<?php
layout_page_end();
