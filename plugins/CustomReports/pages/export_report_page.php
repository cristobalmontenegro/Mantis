<?php
# 1. BLOQUEO TOTAL DE INTERFERENCIAS
while (ob_get_level()) { ob_end_clean(); }

error_reporting(0);
@ini_set('display_errors', 0);

# Validación de seguridad añadida
access_ensure_global_level( plugin_config_get( 'view_custom_reports_threshold' ) );

# 2. CAPTURA DE DATOS BÁSICOS
$t_report_id = gpc_get_int('report_id');
$t_start     = preg_replace( '/[^0-9\-\/ :]/', '', gpc_get_string('param_period_start', '') );
$t_end       = preg_replace( '/[^0-9\-\/ :]/', '', gpc_get_string('param_period_end', '') );

# Captura de parámetros de la vista (Filtros y Ordenamiento JS)
$t_filter   = gpc_get_string('filter', '');
$t_sort_col = gpc_get_string('sort_col', '');
$t_sort_dir = gpc_get_string('sort_dir', '');

# 3. EJECUCIÓN DE QUERY ORIGINAL
$t_table = plugin_table('reports');
$t_query_meta = "SELECT * FROM $t_table WHERE id = $t_report_id";
$t_res_meta = db_query($t_query_meta);
$t_row = db_fetch_array($t_res_meta);

if ($t_row) {
    $t_sql = htmlspecialchars_decode($t_row['query'], ENT_QUOTES);
    $t_sql = rtrim(trim($t_sql), ';');

    $t_sql = str_ireplace(':period_start', "'" . $t_start . "'", $t_sql);
    $t_sql = str_ireplace(':period_end', "'" . $t_end . "'", $t_sql);

    $t_result = db_query($t_sql);
    
    # 4. PASAR DATOS A UN ARRAY PARA MANIPULACIÓN
    $t_all_rows = array();
    while ($row = db_fetch_array($t_result)) {
        $t_all_rows[] = $row;
    }

    # 5. APLICAR FILTRO INTELIGENTE (Simulando el JS)
    if (!empty($t_filter)) {
        $t_tokens = preg_split('/\s+/', strtolower(trim($t_filter)));
        $t_filtered_rows = array();
        
        foreach ($t_all_rows as $row) {
            $row_text = strtolower(implode(" ", array_values($row)));
            $show_row = true;
            
            foreach ($t_tokens as $token) {
                if (strpos($token, '-') === 0 && strlen($token) > 1) {
                    $term = substr($token, 1);
                    if (strpos($row_text, $term) !== false) {
                        $show_row = false;
                        break;
                    }
                } else {
                    if (strpos($row_text, $token) === false) {
                        $show_row = false;
                        break;
                    }
                }
            }
            if ($show_row) {
                $t_filtered_rows[] = $row;
            }
        }
        $t_all_rows = $t_filtered_rows;
    }

    # 6. APLICAR ORDENAMIENTO (Simulando el JS)
    if ($t_sort_col !== '' && is_numeric($t_sort_col)) {
        $col_idx = (int)$t_sort_col;
        
        usort($t_all_rows, function($a, $b) use ($col_idx, $t_sort_dir) {
            $keys = array_keys($a);
            if (!isset($keys[$col_idx])) return 0;
            $key = $keys[$col_idx];
            
            $valA = $a[$key];
            $valB = $b[$key];
            $direction = ($t_sort_dir === 'desc') ? -1 : 1;
            
            # Ordenar numéricamente si corresponde
            if (is_numeric($valA) && is_numeric($valB)) {
                return ($valA <=> $valB) * $direction;
            }
            return strnatcasecmp($valA, $valB) * $direction;
        });
    }

    # 7. FORZAR DESCARGA DEL CSV FINAL PROCESADO
    $t_filename = "reporte_" . $t_report_id . "_" . date("Ymd") . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $t_filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    $t_out = fopen('php://output', 'w');
    fwrite($t_out, "\xEF\xBB\xBF"); # BOM para Excel

    $first = true;
    foreach ($t_all_rows as $row) {
        if ($first) {
            fputcsv($t_out, array_keys($row), ';');
            $first = false;
        }
        fputcsv($t_out, array_values($row), ';');
    }
    
    fclose($t_out);
    exit(); 
} else {
    echo "Error: Reporte no encontrado.";
    exit();
}