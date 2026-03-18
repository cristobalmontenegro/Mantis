<?php
class CustomReportsPlugin extends MantisPlugin {
    function register() {
        $this->name = 'Mantis Custom Reports';
        $this->description = 'Custom SQL Reports for MantisBT.';
        $this->page = 'config_page';
        $this->version = '2.0.5';
        $this->requires = array( 'MantisCore' => '2.0.0' );
        $this->author = 'Cristobal Montenegro / Vincent Sels';
    }

    function hooks() {
        return array( 
            'EVENT_MENU_MAIN' => 'custom_report_menu',
            'EVENT_LAYOUT_RESOURCES' => 'resources' // Nuevo hook para cargar el JS
        );
    }

    function init() {
        require_once( __DIR__ . DIRECTORY_SEPARATOR . 'CustomReports.API.php' );
        require_once( __DIR__ . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'html_api.php' );
    }

    function resources() {
        // Esto inserta el archivo JS de forma segura para el CSP
        return '<script type="text/javascript" src="' . plugin_file( 'custom_reports.js' ) . '"></script>';
    }

    public function custom_report_menu( $p_event ) {
        if ( access_has_global_level( plugin_config_get( 'view_custom_reports_threshold' ) ) ) {
            return array(
                array(
                    'title' => plugin_lang_get( 'custom_reports' ),
                    'url' => plugin_page( 'custom_reports_page', false ),
                    'icon' => 'fa-file-text-o',
                ),
            );
        }
    }
}