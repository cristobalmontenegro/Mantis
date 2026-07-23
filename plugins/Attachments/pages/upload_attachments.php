<?php
require_api( 'string_api.php' );
require_api( 'access_api.php' );
require_api( 'bug_api.php' );
require_api( 'config_api.php' );
require_api( 'file_api.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'print_api.php' );

$f_bug_id = gpc_get_int( 'bug_id' );
$f_files = gpc_get_file( 'ufile1', null );

# SECURITY: Validate bug exists and user has upload permissions
bug_ensure_exists( $f_bug_id );
if( !file_allow_bug_upload( $f_bug_id, auth_get_current_user_id() ) ) {
    access_denied();
}

# Validate CSRF token
form_security_validate( 'attachments_add' );

if( $f_files !== null && !empty( $f_files ) ) {
    $f_files = helper_array_transpose( $f_files );

    $t_allowed_config = config_get( 'allowed_files' );
    $t_disallowed_config = config_get( 'disallowed_files' );

    $t_allowed_exts = array_filter(array_map('trim', explode(',', strtolower($t_allowed_config))));
    $t_disallowed_exts = array_filter(array_map('trim', explode(',', strtolower($t_disallowed_config))));

    # MIME types that should never be uploaded (defense in depth)
    $t_prohibited_mimes = array(
        'application/x-php', 'text/x-php', 'application/x-httpd-php',
        'application/javascript', 'application/x-msdownload', 'application/x-sh',
        'application/x-executable', 'text/html'
    );

    foreach ( $f_files as $t_file ) {
        # Check for PHP upload errors
        if ( !isset($t_file['error']) || $t_file['error'] !== UPLOAD_ERR_OK ) {
            trigger_error( ERROR_FILE_NOT_ALLOWED, ERROR );
        }

        $t_extension = strtolower( pathinfo( $t_file['name'], PATHINFO_EXTENSION ) );

        # Validate extension against allowed list
        if ( !empty($t_allowed_exts) && !in_array($t_extension, $t_allowed_exts) ) {
            trigger_error( ERROR_FILE_NOT_ALLOWED, ERROR );
        }

        # Validate against blocked extensions
        if ( !empty($t_disallowed_exts) && in_array($t_extension, $t_disallowed_exts) ) {
            trigger_error( ERROR_FILE_NOT_ALLOWED, ERROR );
        }

        # MIME type validation
        if ( file_exists( $t_file['tmp_name'] ) ) {
            $t_finfo = finfo_open(FILEINFO_MIME_TYPE);
            $t_real_mime = finfo_file($t_finfo, $t_file['tmp_name']);
            finfo_close($t_finfo);

            if ( in_array($t_real_mime, $t_prohibited_mimes) ) {
                trigger_error( ERROR_FILE_NOT_ALLOWED, ERROR );
            }
        } else {
            trigger_error( ERROR_FILE_NOT_ALLOWED, ERROR );
        }
    }

    file_attach_files( $f_bug_id, $f_files );
}

form_security_purge( 'attachments_add' );

print_header_redirect( 'view.php?id=' . $f_bug_id );
