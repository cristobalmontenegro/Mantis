<?php
require_api( 'access_api.php' );
require_api( 'bug_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'file_api.php' );
require_api( 'gpc_api.php' );
require_api( 'html_api.php' );
require_api( 'print_api.php' );

if( gpc_isset( 'id' ) ) {
    $f_issue_id = gpc_get_int( 'id' );
} else { 
    $f_issue_id = gpc_get_int( 'bug_id' );
}

$t_allow_file_upload = file_allow_bug_upload( $f_issue_id );

if( $t_allow_file_upload ) {
?>
<div id="div_subida_adjuntos" class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <div class="widget-box widget-color-blue2">
        <div class="widget-header widget-header-small">
            <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-paperclip"></i>
                Subir Documento al Expediente
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
                                    Seleccionar PDF <br />
                                    <span class="small grey">Máximo: <?php print_max_filesize( file_get_max_file_size() ); ?></span>
                                </td>
                                <td class="width-70" style="vertical-align: middle;">
                                    <input type="file" name="ufile1[]" accept=".pdf" style="display: inline-block; padding: 5px;" />
                                    <button type="submit" class="btn btn-primary btn-white btn-round btn-sm pull-right">
                                        <i class="fa fa-upload"></i> Subir Archivo
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
    // Usamos el ID exacto que vi en tu código fuente para Relaciones
    var $relaciones = $('#relationships');
    
    if ($miSeccion.length) {
        if ($relaciones.length) {
            // Se mueve justo ARRIBA del bloque de Relaciones
            $miSeccion.insertBefore($relaciones.closest('.col-md-12'));
        } else {
            // Si el juicio no tiene relaciones, buscamos el primer bloque (Detalles) y nos ponemos después
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
?>