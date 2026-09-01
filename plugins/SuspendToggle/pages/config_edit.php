<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

form_security_validate( 'plugin_SuspendToggle_config_edit' );

$f_threshold = gpc_get_int( 'threshold', MANAGER );
$f_block_status = gpc_get_int( 'block_status_gte', 80 );
$f_content_type = gpc_get_int( 'content_type_required', ON );

plugin_config_set( 'threshold', $f_threshold );
plugin_config_set( 'block_status_gte', $f_block_status );
plugin_config_set( 'content_type_required', $f_content_type );

form_security_purge( 'plugin_SuspendToggle_config_edit' );

print_header_redirect( 'manage_plugin_page.php' );
