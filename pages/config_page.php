<?php
auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'admin_threshold' ) );

layout_page_header( plugin_lang_get( 'configuration' ) );
layout_page_begin();

print_manage_menu();
print_custom_report_config_menu( 'config_page' );
?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <div class="form-container">
        <form action="<?php echo plugin_page( 'config_update' ) ?>" method="post">
            <?php echo form_security_field( 'plugin_CustomReports_config_update' ) ?>
            <div class="widget-box widget-color-blue2">
                <div class="widget-header">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-cogs"></i>
                        <?php echo plugin_lang_get( 'configuration' ) ?>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped">
                                <tr>
                                    <td class="category"><?php echo plugin_lang_get( 'admin_threshold' ) ?></td>
                                    <td>
                                        <select name="admin_threshold" class="input-sm">
                                            <?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'admin_threshold' ) ) ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="category"><?php echo plugin_lang_get( 'view_custom_reports_threshold' ) ?></td>
                                    <td>
                                        <select name="view_custom_reports_threshold" class="input-sm">
                                            <?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'view_custom_reports_threshold' ) ) ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="widget-toolbox padding-8 clearfix">
                        <input type="submit" class="btn btn-primary btn-sm btn-white btn-round" value="<?php echo lang_get( 'update' ) ?>"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php layout_page_end(); ?>