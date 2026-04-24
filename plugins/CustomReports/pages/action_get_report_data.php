<?php
# Limpiamos cualquier salida previa para asegurar un JSON puro
ob_clean();

if ( !access_has_global_level( plugin_config_get( 'view_custom_reports_threshold' ) ) ) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$f_report_id = gpc_get_int( 'report_id' );
$t_custom_reports_table = plugin_table( 'reports' );

$t_query = "SELECT include_period FROM $t_custom_reports_table WHERE id = " . db_param();
$t_result = db_query( $t_query, array( $f_report_id ) );
$t_row = db_fetch_array( $t_result );

# Enviamos el encabezado correcto de JSON
header('Content-Type: application/json');
echo json_encode( array(
    'include_period' => (int)$t_row['include_period']
) );
exit;