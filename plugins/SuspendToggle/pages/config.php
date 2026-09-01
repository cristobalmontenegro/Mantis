<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

layout_page_header( plugin_lang_get( 'title' ) );
layout_page_begin( 'config.php' );
print_manage_menu();

$t_current_threshold = (int)plugin_config_get( 'threshold' );
$t_block_status = (int)plugin_config_get( 'block_status_gte' );
$t_content_type_required = plugin_config_get( 'content_type_required' );

$t_status_options = array(
    80 => 'Resuelto (80)',
    90 => 'Cerrado (90)',
    120 => 'Estado 120',
    150 => 'Estado 150',
);

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
<?php echo form_security_field( 'plugin_SuspendToggle_config_edit' ) ?>
<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
    <h4 class="widget-title lighter">
        <i class="ace-icon fa fa-pause"></i>
        <?php echo plugin_lang_get( 'title' ) ?>
    </h4>
</div>
<div class="widget-body">
<div class="widget-main no-padding">
<div class="table-responsive">
<table class="table table-bordered table-condensed table-striped">
<tr>
    <td class="category" width="50%">
        <?php echo plugin_lang_get( 'threshold_label' ) ?>
    </td>
    <td width="50%">
        <select name="threshold" class="input-sm">
<?php foreach ( $t_access_levels as $t_level => $t_label ): ?>
            <option value="<?php echo $t_level ?>" <?php echo ( $t_current_threshold == $t_level ) ? 'selected="selected"' : '' ?>><?php echo $t_label ?></option>
<?php endforeach; ?>
        </select>
        <p class="help-block"><?php echo plugin_lang_get( 'threshold_desc' ) ?></p>
    </td>
</tr>
<tr>
    <td class="category" width="50%">
        <?php echo plugin_lang_get( 'block_status_label' ) ?>
    </td>
    <td width="50%">
        <select name="block_status_gte" class="input-sm">
            <option value="0" <?php echo ( $t_block_status == 0 ) ? 'selected="selected"' : '' ?>>Sin límite (0)</option>
<?php foreach ( $t_status_options as $t_st => $t_st_label ): ?>
            <option value="<?php echo $t_st ?>" <?php echo ( $t_block_status == $t_st ) ? 'selected="selected"' : '' ?>><?php echo $t_st_label ?></option>
<?php endforeach; ?>
        </select>
        <p class="help-block"><?php echo plugin_lang_get( 'block_status_desc' ) ?></p>
    </td>
</tr>
<tr>
    <td class="category" width="50%">
        <?php echo plugin_lang_get( 'content_type_label' ) ?>
    </td>
    <td width="50%">
        <label>
            <input type="radio" name="content_type_required" value="1" class="ace"
                <?php echo ( $t_content_type_required == ON ) ? 'checked="checked"' : '' ?>>
            <span class="lbl"><?php echo lang_get( 'enabled' ) ?></span>
        </label>
        <label>
            <input type="radio" name="content_type_required" value="0" class="ace"
                <?php echo ( $t_content_type_required != ON ) ? 'checked="checked"' : '' ?>>
            <span class="lbl"><?php echo lang_get( 'disabled' ) ?></span>
        </label>
        <p class="help-block"><?php echo plugin_lang_get( 'content_type_desc' ) ?></p>
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
