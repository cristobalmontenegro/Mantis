<?php
class SuspendTogglePlugin extends MantisPlugin {

    const RESOLUTION_OPEN = 10;
    const RESOLUTION_SUSPENDED = 80;

    function register() {
        $this->name        = 'Suspend Toggle';
        $this->description = 'Toggle la resolución de un caso entre Abierto y Suspendido';
        $this->version     = '1.1';
        $this->requires    = array( 'MantisCore' => '2.0.0' );
        $this->author      = 'Cristobal Montenegro';
        $this->page        = 'config';
        $this->schema      = 2;
    }

    function config() {
        return array(
            'threshold' => MANAGER,
            'block_status_gte' => 80,
            'content_type_required' => ON,
        );
    }

    function install() {
        plugin_config_set( 'block_status_gte', 80 );
        plugin_config_set( 'content_type_required', ON );
        return true;
    }

    function upgrade( $p_schema ) {
        if ( (int)$p_schema < 2 ) {
            plugin_config_set( 'block_status_gte', 80 );
            plugin_config_set( 'content_type_required', ON );
        }
        return true;
    }

    function hooks() {
        return array(
            'EVENT_VIEW_BUG_DETAILS' => 'view_bug_details',
            'EVENT_REST_API_ROUTES' => 'routes',
            'EVENT_LAYOUT_RESOURCES' => 'layout_resources',
        );
    }

    function layout_resources( $p_event ) {
        $t_bug_id = gpc_get_int( 'id', 0 );
        if( $t_bug_id <= 0 ) {
            return '';
        }

        $t_user_id = auth_get_current_user_id();
        if( $t_user_id <= 0 ) {
            return '';
        }

        $t_project_id = bug_get_field( $t_bug_id, 'project_id' );
        if( !$t_project_id || !$this->is_toggle_allowed( $t_bug_id, $t_user_id, $t_project_id ) ) {
            return '';
        }

        return '<script src="' . plugin_file( 'suspendtoggle.js', true ) . '"></script>';
    }

    function can_toggle( $p_user_id, $p_project_id ) {
        plugin_push_current( 'SuspendToggle' );
        $t_threshold = (int)plugin_config_get( 'threshold' );
        plugin_pop_current();
        return access_has_project_level( $t_threshold, $p_project_id, $p_user_id );
    }

    function get_block_status() {
        plugin_push_current( 'SuspendToggle' );
        $t_status = (int)plugin_config_get( 'block_status_gte' );
        plugin_pop_current();
        return $t_status;
    }

    function is_toggle_allowed( $p_bug_id, $p_user_id, $p_project_id ) {
        if ( !$this->can_toggle( $p_user_id, $p_project_id ) ) {
            return false;
        }
        if ( $this->get_block_status() > 0 ) {
            $t_status = (int)bug_get_field( $p_bug_id, 'status' );
            if ( $t_status >= $this->get_block_status() ) {
                return false;
            }
        }
        return true;
    }

    function is_same_origin( $p_request ) {
        $t_origin = (string)$p_request->getHeaderLine( 'Origin' );
        if ( is_blank( $t_origin ) ) {
            $t_origin = (string)$p_request->getHeaderLine( 'Referer' );
        }
        if ( is_blank( $t_origin ) ) {
            return true;
        }

        $t_allowed = config_get_global( 'path' );
        $t_host = parse_url( $t_origin, PHP_URL_HOST );
        $t_port = parse_url( $t_origin, PHP_URL_PORT );
        $t_a_host = parse_url( $t_allowed, PHP_URL_HOST );
        $t_a_port = parse_url( $t_allowed, PHP_URL_PORT );

        if ( is_string( $t_host ) && is_string( $t_a_host ) && $t_host === $t_a_host
            && ( (int)$t_port ?: 80 ) === ( (int)$t_a_port ?: 80 ) ) {
            return true;
        }
        return false;
    }

    function content_type_required() {
        plugin_push_current( 'SuspendToggle' );
        $t_required = (bool)plugin_config_get( 'content_type_required' );
        plugin_pop_current();
        return $t_required;
    }

    function view_bug_details( $p_event, $p_bug_id = null ) {
        $t_bug_id = (int)$p_bug_id;
        if ( $t_bug_id <= 0 ) {
            return;
        }

        $t_user_id = auth_get_current_user_id();
        if ( $t_user_id <= 0 ) {
            return;
        }

        $t_project_id = bug_get_field( $t_bug_id, 'project_id' );
        if ( !$t_project_id ) {
            return;
        }

        if ( !$this->is_toggle_allowed( $t_bug_id, $t_user_id, $t_project_id ) ) {
            return;
        }

        $t_resolution = bug_get_field( $t_bug_id, 'resolution' );
        $t_suspended = ( (int)$t_resolution == self::RESOLUTION_SUSPENDED );

        echo '<div class="space-6"></div>';
        echo '<div class="btn-group">';
        echo '<button type="button" class="btn btn-warning btn-white btn-round" id="suspendtoggle-btn" '
           . 'data-bug-id="' . $t_bug_id . '" data-suspended="' . ( $t_suspended ? '1' : '0' ) . '" '
           . 'data-label-suspend="' . plugin_lang_get( 'suspend_button' ) . '" '
           . 'data-label-resume="' . plugin_lang_get( 'resume_button' ) . '" '
           . 'data-msg-suspended="' . plugin_lang_get( 'msg_suspended' ) . '" '
           . 'data-msg-resumed="' . plugin_lang_get( 'msg_resumed' ) . '" '
           . 'data-msg-error="' . plugin_lang_get( 'msg_error' ) . '">';
        echo $t_suspended ? plugin_lang_get( 'resume_button' ) : plugin_lang_get( 'suspend_button' );
        echo '</button>';
        echo '<div class="space-2"></div>';
        echo '<div class="label label-warning" id="suspendtoggle-status" style="display:none"></div>';
        echo '</div>';
    }

    function routes( $p_event_name, $p_event_args ) {
        $t_app = $p_event_args['app'];
        $t_plugin = $this;
        $t_app->group( plugin_route_group(), function() use ( $t_app, $t_plugin ) {
            $t_app->post( '/toggle', function( $p_request, $p_response, $p_args ) use ( $t_plugin ) {
                $t_content_type = $p_request->getHeaderLine( 'Content-Type' );
                if ( $t_plugin->content_type_required()
                    && stripos( $t_content_type, 'application/json' ) === false ) {
                    return $p_response->withStatus( HTTP_STATUS_BAD_REQUEST, 'Content-Type must be application/json' );
                }

                $t_params = $p_request->getParsedBody();

                $t_user_id = auth_is_user_authenticated() ? auth_get_current_user_id() : 0;
                if ( $t_user_id <= 0 ) {
                    return $p_response->withStatus( HTTP_STATUS_UNAUTHORIZED, 'Not authenticated' );
                }

                if ( !$t_plugin->is_same_origin( $p_request ) ) {
                    return $p_response->withStatus( HTTP_STATUS_FORBIDDEN, 'Cross-origin request rejected' );
                }

                $t_bug_id = (int)( $t_params['bug_id'] ?? 0 );

                if ( $t_bug_id <= 0 || !bug_exists( $t_bug_id ) ) {
                    return $p_response->withStatus( HTTP_STATUS_BAD_REQUEST, 'Invalid bug_id' );
                }

                if ( bug_is_readonly( $t_bug_id ) ) {
                    return $p_response->withStatus( HTTP_STATUS_FORBIDDEN, 'Issue is readonly' );
                }

                $t_project_id = bug_get_field( $t_bug_id, 'project_id' );

                if ( !$t_plugin->is_toggle_allowed( $t_bug_id, $t_user_id, $t_project_id ) ) {
                    return $p_response->withStatus( HTTP_STATUS_FORBIDDEN, 'Not allowed to toggle suspension' );
                }

                $t_bug = bug_get( $t_bug_id, true );
                $t_new_resolution = ( (int)$t_bug->resolution == SuspendTogglePlugin::RESOLUTION_SUSPENDED )
                    ? SuspendTogglePlugin::RESOLUTION_OPEN
                    : SuspendTogglePlugin::RESOLUTION_SUSPENDED;

                $t_bug->resolution = $t_new_resolution;
                $t_bug->update( false, false );

                $t_status = ( $t_new_resolution == SuspendTogglePlugin::RESOLUTION_SUSPENDED )
                    ? 'suspended' : 'resumed';

                return $p_response->withStatus( HTTP_STATUS_SUCCESS )->withJson( array(
                    'success' => true,
                    'status' => $t_status,
                    'resolution' => $t_new_resolution,
                ) );
            } );
        } );
    }
}
