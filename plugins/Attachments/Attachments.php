<?php
class AttachmentsPlugin extends MantisPlugin {

    function register() {
        $this->name        = 'Attachments';
        $this->description = plugin_lang_get( 'title' );
        $this->version     = '3.1';
        $this->requires    = array('MantisCore' => '2.0.0');
        $this->author      = 'Cristobal Montenegro basado en el trabajo de Cas Nuy';
        $this->page        = 'config';
    }

    function config() {
        return array('customized' => OFF);
    }

    function init() {
        if ( ON === plugin_config_get( 'customized' ) ) {
            plugin_event_hook( 'EVENT_VIEW_BUG_DETAILS', 'attachment_form' );
        } else {
            plugin_event_hook( 'EVENT_VIEW_BUG_EXTRA', 'attachment_form' );
        }
        plugin_event_hook( 'EVENT_VIEW_BUG_ATTACHMENT', 'show_attachment_date' );
    }

    function attachment_form( $p_event, $p_bug_id = null ) {
        $t_bug_id = $p_bug_id;
        include 'plugins/Attachments/pages/attachments_form.php';
    }

    function show_attachment_date( $p_event, $p_attachment ) {
        $t_date_format = config_get( 'normal_date_format' );
        $t_date = date( $t_date_format, (int) $p_attachment['date_added'] );
        return '<span class="attachment-upload-date"> (' . $t_date . ')</span>';
    }
}