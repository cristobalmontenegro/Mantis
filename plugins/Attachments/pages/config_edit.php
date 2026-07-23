<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

form_security_validate( 'plugin_Attachments_config_edit' );

$f_customized = gpc_get_int( 'customized', OFF );
$f_pdf_only = gpc_get_int( 'pdf_only', ON );

plugin_config_set( 'customized', $f_customized );
plugin_config_set( 'pdf_only', $f_pdf_only );

form_security_purge( 'plugin_Attachments_config_edit' );

print_header_redirect( "manage_plugin_page.php" );
