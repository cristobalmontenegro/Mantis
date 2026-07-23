<?php
require_api( 'string_api.php' );
require_api( 'constant_inc.php' );
require_api( 'file_api.php' );
require_api( 'gpc_api.php' );
require_api( 'html_api.php' );
require_api( 'print_api.php' );

# Get bug ID from hook parameter or POST/GET
$f_issue_id = ( isset( $t_bug_id ) && !empty( $t_bug_id ) ) ? $t_bug_id : gpc_get_int( 'id', gpc_get_int( 'bug_id', 0 ) );

if( file_allow_bug_upload( $f_issue_id ) ) {
    $t_customized = plugin_config_get( 'customized' );
    $t_pdf_only = plugin_config_get( 'pdf_only' );

    # MODE: Customized - Integrates as a row in the native table
    if ( ON === $t_customized ) { ?>
        <tr class="noprint">
            <th class="category">
                <i class="fa fa-paperclip"></i> <?php echo plugin_lang_get( 'add_file_button' ) ?>
            </th>
            <td colspan="5">
                <form method="post" action="<?php echo plugin_page( 'upload_attachments' ); ?>" enctype="multipart/form-data" style="display: inline;">
                    <?php echo form_security_field( 'attachments_add' ); ?>
                    <input type="hidden" name="bug_id" value="<?php echo $f_issue_id ?>" />
                    <?php if( $t_pdf_only ) { ?>
                        <input type="file" name="ufile1[]" accept=".pdf" required style="display: inline-block;" />
                    <?php } else { ?>
                        <input type="file" name="ufile1[]" required style="display: inline-block;" />
                    <?php } ?>
                    <button type="submit" class="btn btn-primary btn-white btn-round btn-sm">
                        <i class="fa fa-upload"></i> <?php echo plugin_lang_get( 'upload_btn' ) ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php }
    # MODE: Default - Standalone block
    else { ?>
        <div id="div_subida_adjuntos" class="col-md-12 col-xs-12">
            <div class="space-10"></div>
            <div class="widget-box widget-color-blue2">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title lighter">
                        <i class="ace-icon fa fa-paperclip"></i> <?php echo plugin_lang_get( 'upload_title' ) ?>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <form method="post" action="<?php echo plugin_page( 'upload_attachments' ); ?>" enctype="multipart/form-data" style="margin: 0;">
                            <?php echo form_security_field( 'attachments_add' ); ?>
                            <input type="hidden" name="bug_id" value="<?php echo $f_issue_id ?>" />
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;">
                                    <tr>
                                        <td class="category width-30" style="vertical-align: middle;">
                                            <?php echo $t_pdf_only ? plugin_lang_get( 'select_pdf' ) : plugin_lang_get( 'select_file' ) ?> <br />
                                            <span class="small grey"><?php echo plugin_lang_get( 'max_size' ) ?> <?php print_max_filesize( file_get_max_file_size() ); ?></span>
                                        </td>
                                        <td class="width-70" style="vertical-align: middle;">
                                            <?php if( $t_pdf_only ) { ?>
                                                <input type="file" name="ufile1[]" accept=".pdf" required style="display: inline-block; padding: 5px;" />
                                            <?php } else { ?>
                                                <input type="file" name="ufile1[]" required style="display: inline-block; padding: 5px;" />
                                            <?php } ?>
                                            <button type="submit" class="btn btn-primary btn-white btn-round btn-sm pull-right">
                                                <i class="fa fa-upload"></i> <?php echo plugin_lang_get( 'upload_btn' ) ?>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        $(document).ready(function() {
            var $miSeccion = $('#div_subida_adjuntos');
            var $relaciones = $('#relationships');
            if ($miSeccion.length) {
                if ($relaciones.length) {
                    $miSeccion.insertBefore($relaciones.closest('.col-md-12'));
                } else {
                    var $primerBloque = $('.page-content .row > .col-md-12').first();
                    if ($primerBloque.length) {
                        $miSeccion.insertAfter($primerBloque);
                    }
                }
            }
        });
        </script>
    <?php
    }
}
?>
