<?php
auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'admin_threshold' ) );

$f_report_id = gpc_get_int( 'report_id', null );
$t_report = array('name' => '', 'view_threshold' => 10, 'query' => '', 'include_period' => 0);

if ( !empty( $f_report_id ) ) {
    $t_reports_table = plugin_table( 'reports' );
    $t_query = "SELECT * FROM $t_reports_table WHERE id = " . db_param();
    $t_result = db_query( $t_query, array( $f_report_id ) );
    $t_report = db_fetch_array( $t_result );
}

layout_page_header( plugin_lang_get( 'manage_reports' ) );
layout_page_begin();
print_manage_menu();
?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <form method="post" action="<?php echo plugin_page( 'manage_reports_update' ) ?>">
        <?php echo form_security_field( 'plugin_CustomReports_update' ) ?>
        <input type="hidden" name="report_id" value="<?php echo $f_report_id ?>" />

        <div class="widget-box widget-color-blue2">
            <div class="widget-header">
                <h4 class="widget-title lighter"><?php echo plugin_lang_get( 'manage_reports' ) ?></h4>
            </div>
            <div class="widget-body">
                <div class="widget-main no-padding">
                    <table class="table table-bordered table-condensed table-striped">
                        <tr>
                            <td class="category"><?php echo plugin_lang_get( 'report_name' ) ?></td>
                            <td><input type="text" size="64" name="report_name" class="input-sm form-control" value="<?php echo $t_report['name'] ?>" /></td>
                        </tr>
                        <tr>
                            <td class="category"><?php echo plugin_lang_get( 'view_threshold' ) ?></td>
                            <td>
                                <select name="view_threshold" class="input-sm">
                                    <?php print_enum_string_option_list( 'access_levels', $t_report['view_threshold'] ) ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="category"><?php echo plugin_lang_get( 'query' ) ?></td>
                            <td><textarea rows="8" name="query" class="form-control"><?php echo $t_report['query'] ?></textarea></td>
                        </tr>
                        <tr>
                            <td class="category"><?php echo plugin_lang_get( 'include_period_parameters' ) ?></td>
                            <td>
                                <label>
                                    <input type="checkbox" name="include_period" class="ace" <?php echo $t_report['include_period'] ? "checked" : "" ?> />
                                    <span class="lbl"> <?php echo plugin_lang_get( 'include_period_parameters_info' ) ?></span>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="widget-toolbox padding-8 clearfix">
                    <input type="submit" class="btn btn-primary btn-sm btn-white btn-round" value="<?php echo lang_get( 'update' ) ?>"/>
                </div>
            </div>
        </div>
    </form>
</div>

<?php layout_page_end(); ?>