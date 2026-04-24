jQuery(document).ready(function() {
    // Función para activar los calendarios
    function activateCalendars() {
        var startInput = jQuery('#param_period_start');
        var endInput = jQuery('#param_period_end');

        // Intento 1: Usar la librería de Mantis (bootstrap-datepicker)
        if (typeof jQuery.fn.datepicker !== 'undefined') {
            jQuery('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
            console.log("CustomReports: Datepicker de Bootstrap activado.");
        } 
        // Intento 2: Usar datetimepicker (algunas versiones de Mantis 2.x lo usan)
        else if (typeof jQuery.fn.datetimepicker !== 'undefined') {
            jQuery('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                keepOpen: false
            });
            console.log("CustomReports: Datetimepicker activado.");
        }
        // Intento 3: Respaldo nativo si no hay librerías cargadas
        else {
            console.log("CustomReports: No se detectó librería JS, usando calendario nativo.");
            startInput.attr('type', 'date');
            endInput.attr('type', 'date');
        }
    }

    // Al hacer clic en el icono del calendario, enfocar el campo
    jQuery(document).on('click', '.input-group-addon', function() {
        jQuery(this).parent().find('input').focus();
    });

    function checkPeriod() {
        var reportSelect = jQuery('#report_id_select');
        var reportId = reportSelect.val();
        var periodContainer = jQuery('#period_container');
        var ajaxUrl = reportSelect.attr('data-url');

        if(!reportId || !ajaxUrl) {
            periodContainer.hide();
            return;
        }

        jQuery.getJSON(ajaxUrl, { report_id: reportId })
            .done(function(data) {
                if (data && (data.include_period == 1 || data.include_period == "1")) {
                    periodContainer.css('display', 'inline-block');
                    activateCalendars(); // Forzar activación al mostrar
                } else {
                    periodContainer.hide();
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error en CustomReports AJAX: " + textStatus);
            });
    }

    jQuery('#report_id_select').change(checkPeriod);
    
    // Ejecución inicial
    checkPeriod();
    activateCalendars();
});