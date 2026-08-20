<?php

# Copyright (c) BeYs Cloud - 2024. This program is proposed under GPLv3
# See LICENSE.txt for more informations.
# Original: https://github.com/be-ys-cloud/MantisBT-TOTP-Plugin
# Adapted and improved by Cristobal Montenegro - https://github.com/cristobalmontenegro

require_once(__DIR__ . '/vendor/phpqrcode/phpqrcode.php');
require_once(__DIR__ . '/vendor/php-totp/Base32.php');
require_once(__DIR__ . '/vendor/php-totp/HOTP.php');
require_once(__DIR__ . '/vendor/php-totp/TOTP.php');
require_once(__DIR__ . '/core/database.php');

class TOTPPlugin extends MantisPlugin
{

    // register plugin informations
    function register()
    {
        $this->name = plugin_lang_get('title');
        $this->description = plugin_lang_get('description');

        $this->version = '1.2.0';
        $this->requires = array(
            'MantisCore' => '2.0.0',
        );

        $this->author = 'Cristobal Montenegro - basado en el trabajo de BeYs Cloud';
        $this->contact = 'cmc@socorropc.com';
        $this->url = 'https://github.com/cristobalmontenegro';
        $this->page = 'config';
    }

    // Create required SQL scheme
    function schema()
    {
        $t_table_options = array(
            'mysql' => 'DEFAULT CHARSET=utf8',
            'pgsql' => 'WITHOUT OIDS',
        );

        return array(
            array("CreateTableSQL", array(plugin_table("totp"), "
          user_id I NOT NULL UNIQUE,
          secret_key C(2000) NOT NULL UNIQUE
        ", $t_table_options)),
            array("CreateTableSQL", array(plugin_table("backup_codes"), "
          id I NOT NULL AUTO,
          user_id I NOT NULL,
          code_hash C(255) NOT NULL,
          used I NOT NULL DEFAULT 0,
          PRIMARY KEY (id)
        ", $t_table_options)),
            array("CreateTableSQL", array(plugin_table("failed_attempts"), "
          id I NOT NULL AUTO,
          user_id I NOT NULL,
          ip_address C(45) NOT NULL,
          attempted_at I NOT NULL,
          PRIMARY KEY (id)
        ", $t_table_options)),
            array("CreateTableSQL", array(plugin_table("remembered_devices"), "
          id I NOT NULL AUTO,
          user_id I NOT NULL,
          device_hash C(255) NOT NULL,
          expires_at I NOT NULL,
          PRIMARY KEY (id)
        ", $t_table_options)),
        );
    }

    // Plugin general configuration
    function config()
    {
        return array(
            'issuer' => 'MantisBt Bug Tracker',
            'max_failed_attempts' => 5,
            'lockout_duration' => 900,
            'remember_device_days' => 30,
            'backup_codes_count' => 8,
        );
    }

    // List impacted hooks
    function hooks()
    {
        $t_hooks = array(
            'EVENT_AUTH_USER_FLAGS' => 'auth_user_flags',
            'EVENT_MANAGE_USER_PAGE' => 'manage_user_page',
            'EVENT_MENU_ACCOUNT' => 'account_menu',
        );

        return $t_hooks;
    }

    // Add "Manage TOTP" in user account
    function account_menu(){
            return array( '<a href="' . plugin_page( 'manage-totp' ) . '">' . plugin_lang_get( 'manage' ) .  '</a>', );
    }

    // Show TOTP status on admin manage user page
    function manage_user_page($p_event_name, $p_user_id)
    {
        $t_user_id = (int)$p_user_id;
        $t_totp_enabled = isUserTOTPConfigured($t_user_id);

        echo '<div class="space-10"></div>';
        echo '<div id="totp-status-div" class="form-container">';
        echo '<h4>' . plugin_lang_get('manage') . '</h4>';
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered table-condensed">';
        echo '<tr>';
        echo '<th class="category" style="width:40%">Estado TOTP</th>';
        echo '<td>';
        if ($t_totp_enabled) {
            echo '<span class="label label-success">' . plugin_lang_get('totp_enabled') . '</span>';
        } else {
            echo '<span class="label label-default">' . plugin_lang_get('totp_not_enabled') . '</span>';
        }
        echo '</td>';
        echo '</tr>';
        if ($t_totp_enabled) {
            echo '<tr>';
            echo '<th class="category">Acciones</th>';
            echo '<td>';
            echo '<form action="' . plugin_page('admin-disable-totp') . '" method="post" onsubmit="return confirm(\'' . addslashes(plugin_lang_get('totp_admin_disable_confirm')) . '\')">';
            echo form_security_field('plugin_totp_admin_disable');
            echo '<input type="hidden" name="user_id" value="' . $t_user_id . '" />';
            echo '<input type="submit" value="' . plugin_lang_get('totp_admin_disable_button') . '" ';
            echo 'class="btn btn-danger" />';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    // Handle custom authentication
    function auth_user_flags($p_event_name, $p_args)
    {
        $t_username = $p_args['username'];
        $t_user_id = $p_args['user_id'];

        if (!$t_user_id || user_is_anonymous($t_user_id)) {
            return null;
        }

        if(!isUserTOTPConfigured($t_user_id)) {
            return null;
        }

        // Check if device is remembered
        $t_cookie_name = config_get_global('cookie_prefix') . '_totp_remember_' . $t_user_id;
        if (isset($_COOKIE[$t_cookie_name])) {
            $t_stored_hash = $_COOKIE[$t_cookie_name];
            if (isDeviceRemembered($t_user_id, $t_stored_hash)) {
                return null;
            }
        }

        $t_flags = new AuthFlags();
        $t_flags->setCredentialsPage(helper_url_combine(plugin_page('login-totp', true), 'username=' . $t_username));
        return $t_flags;
    }
}
