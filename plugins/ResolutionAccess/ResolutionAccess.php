<?php
class ResolutionAccessPlugin extends MantisPlugin {

    function register() {
        $this->name        = 'Resolution Access Control';
        $this->description = 'Control resolution field access per role';
        $this->version     = '1.0';
        $this->requires    = array( 'MantisCore' => '2.0.0' );
        $this->author      = 'cmc';
        $this->page        = 'config';
    }

    function config() {
        return array(
            'readonly_threshold' => UPDATER,
        );
    }

    function hooks() {
        return array(
            'EVENT_UPDATE_BUG_DATA' => 'update_bug_data',
            'EVENT_LAYOUT_RESOURCES' => 'layout_resources',
        );
    }

    function update_bug_data( $p_event, $p_updated_bug, $p_existing_bug ) {
        $t_project_id = $p_updated_bug->project_id;
        $t_user_id = auth_get_current_user_id();
        $t_access_level = access_get_project_level( $t_project_id, $t_user_id );
        $t_threshold = (int)plugin_config_get( 'readonly_threshold' );

        if ( $t_access_level <= $t_threshold ) {
            $p_updated_bug->resolution = $p_existing_bug->resolution;
        }

        return $p_updated_bug;
    }

    function layout_resources( $p_event ) {
        $t_script_name = $_SERVER['SCRIPT_NAME'] ?? '';

        if ( strpos( $t_script_name, 'bug_update_page.php' ) === false
          && strpos( $t_script_name, 'bug_change_status_page.php' ) === false ) {
            return '';
        }

        $t_bug_id = gpc_get_int( 'bug_id', gpc_get_int( 'id', 0 ) );
        if ( $t_bug_id <= 0 ) {
            return '';
        }

        $t_user_id = auth_get_current_user_id();
        if ( $t_user_id <= 0 ) {
            return '';
        }

        $t_project_id = bug_get_field( $t_bug_id, 'project_id' );
        if ( !$t_project_id ) {
            return '';
        }

        $t_access_level = access_get_project_level( $t_project_id, $t_user_id );
        $t_threshold = (int)plugin_config_get( 'readonly_threshold' );

        if ( $t_access_level > $t_threshold ) {
            return '';
        }

        return '<style>#resolution,select[name="resolution"]{pointer-events:none!important;opacity:.6;background:#eee}</style>';
    }
}