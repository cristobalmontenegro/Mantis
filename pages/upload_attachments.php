<?php
# Al ser llamado por el enrutador del plugin, ya estamos en la raíz de Mantis
require_api( 'file_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'print_api.php' );

$f_bug_id = gpc_get_int( 'bug_id' );
$f_files = gpc_get_file( 'ufile1', null );

if( $f_files !== null && !empty( $f_files ) ) {
    $f_files = helper_array_transpose( $f_files );
    
    if( $f_files ) {
        $user = auth_get_current_user_id();
        if( !file_allow_bug_upload( $f_bug_id, $user ) ) {
            access_denied();
        }
    }

    # Sube el archivo a tu carpeta /archivos/mantis configurada
    file_attach_files( $f_bug_id, $f_files );
}

# Redirige de vuelta al juicio de forma segura
print_header_redirect( 'view.php?id=' . $f_bug_id );