<?php
# No tocamos buffers aquí para no romper la carga de la página
access_ensure_global_level( plugin_config_get( 'view_custom_reports_threshold' ) );

$f_selected_report_id = gpc_get_int( 'report_id', 0 );
$f_param_period_start = gpc_get_string( 'param_period_start', first_day_of_month( -1 ) );
$f_param_period_end   = gpc_get_string( 'param_period_end', last_day_of_month( -1 ) );

$t_result = array();
$f_selected_report = array();

if ( $f_selected_report_id > 0 ) {
    $t_custom_reports_table = plugin_table( 'reports' );
    $t_query_meta = "SELECT * FROM $t_custom_reports_table WHERE id = " . db_param();
    $t_query_result = db_query( $t_query_meta, array( $f_selected_report_id ) );
    $f_selected_report = db_fetch_array( $t_query_result );

    if ( $f_selected_report ) {
        $t_report_query = htmlspecialchars_decode( $f_selected_report['query'], ENT_QUOTES );
        $t_report_query = rtrim( trim($t_report_query), ';' );

        # Lógica de parámetros para la visualización en tabla
        if ( !empty($f_param_period_start) && !empty($f_param_period_end) ) {
            $t_report_query = str_ireplace( ':period_start', "'" . $f_param_period_start . "'", $t_report_query );
            $t_report_query = str_ireplace( ':period_end', "'" . $f_param_period_end . "'", $t_report_query );
        }

        $t_report_result = db_query( $t_report_query );
        while ( $row = db_fetch_array( $t_report_result ) ) {
            $t_result[] = $row;
        }
    }
}

layout_page_header( lang_get( 'plugin_CustomReports_custom_reports' ) );
layout_page_begin();
?>

<div class="col-md-12 col-xs-12">
    <div class="widget-box widget-color-blue2">
        <div class="widget-header">
            <h4 class="widget-title lighter"><i class="fa fa-filter"></i> <?php echo lang_get( 'plugin_CustomReports_custom_report' ) ?></h4>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <form action="<?php echo plugin_page( 'custom_reports_page' ) ?>" method="post" class="form-inline">
                    <select id="report_id_select" name="report_id" class="form-control input-sm" data-url="<?php echo plugin_page( 'action_get_report_data', false ) ?>">
                        <?php print_custom_report_option_list( $f_selected_report_id ) ?>
                    </select>

                    <div id="period_container" style="display: none; margin-left: 20px;">
                        <label><?php echo lang_get( 'plugin_CustomReports_period_start' ) ?>: </label>
                        <input type="text" id="param_period_start" name="param_period_start" value="<?php echo $f_param_period_start ?>" class="form-control input-sm datepicker">
                        <label style="margin-left: 10px;"> <?php echo lang_get( 'plugin_CustomReports_period_end' ) ?>: </label>
                        <input type="text" id="param_period_end" name="param_period_end" value="<?php echo $f_param_period_end ?>" class="form-control input-sm datepicker">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm btn-round" style="margin-left: 15px;">
                        <i class="fa fa-refresh"></i> <?php echo lang_get( 'plugin_CustomReports_load_report' ) ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php if ( count( $t_result ) > 0 ) { ?>
        <div class="space-10"></div>
        <div class="widget-box widget-color-blue2">
            <div class="widget-header widget-header-small">
                <h4 class="widget-title lighter"><i class="fa fa-table"></i> <?php echo string_display_line($f_selected_report['name']); ?></h4>
                <div class="widget-toolbar no-border">
                    <a href="<?php echo plugin_page( 'export_report_page' ) . '&report_id=' . $f_selected_report_id . '&param_period_start=' . $f_param_period_start . '&param_period_end=' . $f_param_period_end; ?>" 
                       class="btn btn-sm btn-success btn-round" style="background-color: #449d44 !important; border-color: #398439 !important; color: white !important;">
                        <i class="fa fa-file-excel-o"></i> <strong><?php echo lang_get( 'plugin_CustomReports_export_to_excel' ) ?></strong>
                    </a>
                </div>
            </div>
            <div class="widget-body">
                <div class="widget-main no-padding">
                    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <tr class="row-category">
                                    <?php foreach (array_keys($t_result[0]) as $k) echo "<th>" . string_display_line($k) . "</th>"; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($t_result as $r) {
                                    echo "<tr>";
                                    foreach ($r as $v) echo "<td>" . string_display_line($v) . "</td>";
                                    echo "</tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php layout_page_end(); ?>