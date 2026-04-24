<?php
form_security_validate( 'plugin_CustomReports_delete' );

auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'admin_threshold' ) );

$f_report_id = gpc_get_int( 'report_id', null );

if ( !empty( $f_report_id ) ) {
    $t_reports_table = plugin_table( 'reports' );
    # Uso de db_param() para Mantis 2.x
    $t_query = "DELETE FROM $t_reports_table WHERE id = " . db_param();
    db_query( $t_query, array( $f_report_id ) );
}

form_security_purge( 'plugin_CustomReports_delete' );

# Redirección limpia
print_successful_redirect( plugin_page( 'manage_reports_overview_page', true ) );