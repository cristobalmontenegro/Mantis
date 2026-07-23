<?php
class BugRelationPriorityPlugin extends MantisPlugin {

    function register() {
        $this->name        = 'Prioridad en Relaciones';
        $this->description = 'Agrega una columna con la prioridad del caso en la tabla de relaciones.';
        $this->version     = '2.0.3'; // Versión Segura para Producción
        $this->requires    = array('MantisCore' => '2.0.0');
        $this->author      = 'Cristobal Montenegro';
        $this->contact     = '';
        $this->url         = 'https://github.com/cristobalmontenegro';
    }

    function hooks() {
        return array(
            'EVENT_LAYOUT_RESOURCES'   => 'inject_js_file',
            'EVENT_LAYOUT_PAGE_FOOTER' => 'inject_priority_data',
        );
    }

    function inject_js_file( $p_event ) {
        if ( basename($_SERVER['SCRIPT_NAME']) === 'view.php' ) {
            return '<script src="' . htmlspecialchars( plugin_file( 'priority.js' ), ENT_QUOTES, 'UTF-8' ) . '"></script>';
        }
    }

    function inject_priority_data( $p_event ) {
        if ( basename($_SERVER['SCRIPT_NAME']) !== 'view.php' ) { return; }
        if ( !isset( $_GET['id'] ) || !is_numeric( $_GET['id'] ) ) { return; }

        $t_bug_id = (int)$_GET['id'];

        require_api( 'database_api.php' );
        require_api( 'bug_api.php' );
        require_api( 'lang_api.php' );
        require_api( 'access_api.php' ); // <-- Añadimos la API de seguridad y permisos
        
        $t_priority_map = array();

        $t_query = "SELECT b.id, b.priority 
                    FROM {bug} b
                    JOIN {bug_relationship} r 
                      ON (b.id = r.source_bug_id OR b.id = r.destination_bug_id)
                    WHERE (r.source_bug_id = " . db_param() . " OR r.destination_bug_id = " . db_param() . ")
                      AND b.id != " . db_param();
                      
        $t_result = db_query( $t_query, array( $t_bug_id, $t_bug_id, $t_bug_id ) );

        while( $t_row = db_fetch_array( $t_result ) ) {
            $t_rel_bug_id = (int)$t_row['id'];
            $t_priority_val = (int)$t_row['priority'];
            
            // <-- EL BLOQUEO DE SEGURIDAD: Solo procesa la prioridad si el usuario tiene permiso de ver el caso
            if ( bug_exists( $t_rel_bug_id ) && access_has_bug_level( config_get( 'view_bug_threshold' ), $t_rel_bug_id ) ) {
                $t_priority_text = get_enum_element( 'priority', $t_priority_val );
                if ( !$t_priority_text ) { $t_priority_text = "N/D"; }
                $t_priority_map[$t_rel_bug_id] = $t_priority_text;
            }
        }

        $t_json_priorities = htmlspecialchars(json_encode( $t_priority_map ), ENT_QUOTES, 'UTF-8');

        echo '<div id="bug-relation-priorities-data" data-priorities="' . $t_json_priorities . '" style="display:none;"></div>';
    }
}