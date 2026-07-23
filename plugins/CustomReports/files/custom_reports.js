jQuery(document).ready(function() {
    function activateCalendars() {
        var startInput = jQuery('#param_period_start');
        var endInput = jQuery('#param_period_end');

        if (typeof jQuery.fn.datepicker !== 'undefined') {
            jQuery('.datepicker').datepicker({ autoclose: true, todayHighlight: true, format: 'yyyy-mm-dd' });
        } else if (typeof jQuery.fn.datetimepicker !== 'undefined') {
            jQuery('.datepicker').datetimepicker({ format: 'YYYY-MM-DD', keepOpen: false });
        } else {
            startInput.attr('type', 'date');
            endInput.attr('type', 'date');
        }
    }

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
                    activateCalendars(); 
                } else {
                    periodContainer.hide();
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error en CustomReports AJAX: " + textStatus);
            });
    }

    jQuery('#report_id_select').change(checkPeriod);
    checkPeriod();
    activateCalendars();

    // ==========================================
    // NUEVO: ACTUALIZACIÓN DINÁMICA DE EXPORTACIÓN
    // ==========================================
    var exportBtn = jQuery('#export_btn');
    if (exportBtn.length) {
        exportBtn.data('base-url', exportBtn.attr('href')); // Guardar URL original
    }

    function updateExportUrl() {
        if (!exportBtn.length) return;
        
        var filterVal = encodeURIComponent(jQuery('#smart_filter').val() || '');
        var table = jQuery('#custom_report_table');
        var sortCol = table.data('sort-col');
        var sortDir = table.data('sort-dir');
        
        sortCol = (sortCol !== undefined) ? encodeURIComponent(sortCol) : '';
        sortDir = (sortDir !== undefined) ? encodeURIComponent(sortDir) : '';

        var newUrl = exportBtn.data('base-url') + '&filter=' + filterVal + '&sort_col=' + sortCol + '&sort_dir=' + sortDir;
        exportBtn.attr('href', newUrl);
    }

    // ==========================================
    // FILTRO INTELIGENTE
    // ==========================================
    jQuery('#smart_filter').on('input', function() {
        var searchText = jQuery(this).val().toLowerCase().trim();
        var tokens = searchText.split(/\s+/);

        if (searchText === '') {
            jQuery('#custom_report_table tbody tr').show();
        } else {
            jQuery('#custom_report_table tbody tr').each(function() {
                var rowText = jQuery(this).text().toLowerCase();
                var showRow = true;

                for (var i = 0; i < tokens.length; i++) {
                    var token = tokens[i];
                    if (token.startsWith('-') && token.length > 1) {
                        var term = token.substring(1);
                        if (rowText.indexOf(term) !== -1) { showRow = false; break; }
                    } else {
                        if (rowText.indexOf(token) === -1) { showRow = false; break; }
                    }
                }
                jQuery(this).toggle(showRow);
            });
        }
        updateExportUrl(); // Actualizar enlace Excel
    });

    // ==========================================
    // ORDENAMIENTO POR COLUMNAS
    // ==========================================
    jQuery('.cr-sortable-col').click(function() {
        var table = jQuery('#custom_report_table');
        var tbody = table.find('tbody');
        var rows = tbody.find('tr').toArray();
        var colIndex = jQuery(this).data('col');
        
        var isAsc = jQuery(this).hasClass('asc');
        var direction = isAsc ? -1 : 1; // -1 invierte, 1 normal
        
        // Reset iconos y clases visuales
        table.find('th i').removeClass('fa-sort-asc fa-sort-desc').addClass('fa-sort').css('color', '#ccc');
        table.find('th').removeClass('asc desc');
        
        // Asignar el nuevo orden visual
        if (isAsc) {
            jQuery(this).addClass('desc');
            jQuery(this).find('i').removeClass('fa-sort').addClass('fa-sort-desc').css('color', '#333');
            table.data('sort-dir', 'desc');
        } else {
            jQuery(this).addClass('asc');
            jQuery(this).find('i').removeClass('fa-sort').addClass('fa-sort-asc').css('color', '#333');
            table.data('sort-dir', 'asc');
        }
        table.data('sort-col', colIndex);

        // Función de ordenamiento
        rows.sort(function(a, b) {
            var valA = jQuery(a).children('td').eq(colIndex).text().trim();
            var valB = jQuery(b).children('td').eq(colIndex).text().trim();
            
            // Detección automática de números (como tu campo Monto: 2995190.36)
            var numA = parseFloat(valA.replace(/[^0-9.-]+/g, ""));
            var numB = parseFloat(valB.replace(/[^0-9.-]+/g, ""));
            
            // Si ambas cadenas son interpretadas fielmente como números
            if (!isNaN(numA) && !isNaN(numB) && valA.match(/^[\d.,-]+$/) && valB.match(/^[\d.,-]+$/)) {
                return (numA - numB) * direction;
            }
            // Fallback texto
            return valA.localeCompare(valB) * direction;
        });
        
        // Renderizar nuevo orden
        jQuery.each(rows, function(index, row) {
            tbody.append(row); 
        });
        
        updateExportUrl(); // Actualizar enlace Excel
    });
});