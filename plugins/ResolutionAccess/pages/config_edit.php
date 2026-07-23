<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

form_security_validate( 'plugin_ResolutionAccess_config_edit' );

$f_readonly_threshold = gpc_get_int( 'readonly_threshold', UPDATER );

plugin_config_set( 'readonly_threshold', $f_readonly_threshold );

form_security_purge( 'plugin_ResolutionAccess_config_edit' );

print_header_redirect( 'manage_plugin_page.php' );
