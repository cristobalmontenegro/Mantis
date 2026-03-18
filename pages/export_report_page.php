<?php
# 1. BLOQUEO TOTAL DE INTERFERENCIAS
# Limpiamos cualquier salida previa (espacios en archivos de idioma, warnings, etc.)
while (ob_get_level()) { ob_end_clean(); }

# Forzamos a PHP a no mostrar errores que ensucien el CSV
error_reporting(0);
@ini_set('display_errors', 0);

# 2. CAPTURA DE DATOS (Mismos nombres que usas en el reporte)
$t_report_id = (int)$_GET['report_id'];
$t_start     = $_GET['param_period_start'];
$t_end       = $_GET['param_period_end'];

# 3. EJECUCIÓN DE QUERY
# Usamos las tablas del plugin directamente
$t_table = plugin_table('reports');
$t_query_meta = "SELECT * FROM $t_table WHERE id = $t_report_id";
$t_res_meta = db_query($t_query_meta);
$t_row = db_fetch_array($t_res_meta);

if ($t_row) {
    # Limpiamos la query de caracteres raros
    $t_sql = htmlspecialchars_decode($t_row['query'], ENT_QUOTES);
    $t_sql = rtrim(trim($t_sql), ';');

    # REEMPLAZO MANUAL SEGURO (Mantenemos tu lógica de fechas)
    $t_sql = str_ireplace(':period_start', "'" . $t_start . "'", $t_sql);
    $t_sql = str_ireplace(':period_end', "'" . $t_end . "'", $t_sql);

    $t_result = db_query($t_sql);

    # 4. FORZAR DESCARGA
    $t_filename = "reporte_" . $t_report_id . "_" . date("Ymd") . ".csv";

    # Headers de bajo nivel para engañar a cualquier caché
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $t_filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    $t_out = fopen('php://output', 'w');
    
    # Escribir el BOM (Esto arregla las tildes en Excel)
    fwrite($t_out, "\xEF\xBB\xBF");

    $first = true;
    while ($row = db_fetch_array($t_result)) {
        if ($first) {
            # Encabezados
            fputcsv($t_out, array_keys($row), ';');
            $first = false;
        }
        # Datos
        fputcsv($t_out, array_values($row), ';');
    }
    
    fclose($t_out);
    # MATAMOS EL PROCESO (Para que Mantis no meta su HTML al final)
    exit(); 
} else {
    echo "Error: Reporte no encontrado.";
    exit();
}