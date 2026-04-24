<?php
function print_custom_report_option_list( $p_selected_report = null ) {
    $t_custom_reports = custom_reports_get_all();
    echo '<option value="">--- Seleccione ---</option>';
    foreach ( $t_custom_reports as $t_report ) {
        $t_selected = ( $t_report['id'] == $p_selected_report ) ? 'selected="selected"' : '';
        echo '<option value="' . (int)$t_report['id'] . '" ' . $t_selected . '>' . string_display_line($t_report['name']) . '</option>';
    }
}

function print_custom_report_config_menu( $p_page = '' ) {
    echo '<div class="space-10"></div>';
    echo '<div class="btn-group">';
    if ( access_has_global_level( plugin_config_get( 'admin_threshold' ) ) ) {
        # URLs limpias sin parámetros pegados
        echo '<a class="btn btn-sm btn-primary btn-white btn-round" href="' . plugin_page( 'config_page' ) . '">' . plugin_lang_get( 'general_configuration' ) . '</a>';
        echo '<a class="btn btn-sm btn-primary btn-white btn-round" href="' . plugin_page( 'manage_reports_overview_page' ) . '">' . plugin_lang_get( 'manage_reports' ) . '</a>';
    }
    echo '</div>';
}