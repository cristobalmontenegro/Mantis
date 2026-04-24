<?php
form_security_validate( 'plugin_CustomReports_config_update' );
auth_reauthenticate();
access_ensure_global_level( plugin_config_get( 'admin_threshold' ) );

# [cite_start]Guardamos las opciones directamente para evitar errores de funciones [cite: 3]
$t_admin_threshold = gpc_get_int( 'admin_threshold' );
$t_view_threshold  = gpc_get_int( 'view_custom_reports_threshold' );

if ( $t_admin_threshold != plugin_config_get( 'admin_threshold' ) ) {
    plugin_config_set( 'admin_threshold', $t_admin_threshold );
}
if ( $t_view_threshold != plugin_config_get( 'view_custom_reports_threshold' ) ) {
    plugin_config_set( 'view_custom_reports_threshold', $t_view_threshold );
}

form_security_purge( 'plugin_CustomReports_config_update' );
# [cite_start]Redirecci¨®n limpia [cite: 3]
print_successful_redirect( plugin_page( 'config_page', true ) );