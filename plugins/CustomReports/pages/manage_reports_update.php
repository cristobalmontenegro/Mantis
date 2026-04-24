<?php
form_security_validate( 'plugin_CustomReports_update' );
auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'admin_threshold' ) );

$f_id     = gpc_get_int( 'report_id', null );
$f_name   = gpc_get_string( 'report_name', null );
$f_view   = gpc_get_string( 'view_threshold', null );
# [cite_start]CORRECCIÓN: Obtenemos la query limpia primero [cite: 13]
$f_query_raw = gpc_get_string( 'query', null );
$f_period = ( gpc_get_bool( 'include_period', false ) ? 1 : 0 );

# Validación de seguridad básica
$t_test_query = ' ' . strtoupper( $f_query_raw ) . ' ';
$t_forbidden = array('INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP', 'TRUNCATE');
foreach ( $t_forbidden as $t_keyword ) {
    if ( strpos( $t_test_query, ' ' . $t_keyword . ' ' ) !== false ) {
        form_security_purge( 'plugin_CustomReports_update' );
        print_successful_redirect( plugin_page( 'manage_reports_overview_page', true ) . '&invalid_keywords=true' );
        exit;
    }
}

$t_table = plugin_table( 'reports' );
# [cite_start]Codificamos una sola vez para la base de datos [cite: 13]
$t_query_safe = htmlspecialchars( $f_query_raw, ENT_QUOTES );

if ( empty( $f_id ) ) {
    $q = "INSERT INTO $t_table (name, view_threshold, query, include_period) VALUES (".db_param().", ".db_param().", ".db_param().", ".db_param().")";
    db_query( $q, array( $f_name, $f_view, $t_query_safe, $f_period ) );
} else {
    $q = "UPDATE $t_table SET name=".db_param().", view_threshold=".db_param().", query=".db_param().", include_period=".db_param()." WHERE id=".db_param();
    db_query( $q, array( $f_name, $f_view, $t_query_safe, $f_period, $f_id ) );
}

form_security_purge( 'plugin_CustomReports_update' );
print_successful_redirect( plugin_page( 'manage_reports_overview_page', true ) . '&success=true' );