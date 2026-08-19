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

        $this->version = '1.1.0';
        $this->requires = array(
            'MantisCore' => '2.0.0',
        );

        $this->author = 'Cristobal Montenegro - basado en el trabajo de BeYs Cloud';
        $this->contact = 'cmc@socorropc.com';
        $this->url = 'https://github.com/cristobalmontenegro';
        $this->page = 'config'; # Default plugin page (update)
    }

    // Create required SQL scheme to store informations in database
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
        ", $t_table_options))
        );
    }

    // Plugin general configuration
    function config()
    {
        return array(
            'issuer' => 'MantisBt Bug Tracker',
        );
    }

    // List impacted hooks by our plugin
    function hooks()
    {
        $t_hooks = array(
            'EVENT_AUTH_USER_FLAGS' => 'auth_user_flags',
            'EVENT_MANAGE_USER_PAGE' => 'manage_user_page',
            'EVENT_MENU_ACCOUNT' => 'account_menu',
        );

        return $t_hooks;
    }

    // Add a "Manage TOTP" section in user account
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
        echo '<th class="category" style="width:40%">' . plugin_lang_get('manage') . '</th>';
        echo '<td>';
        if ($t_totp_enabled) {
            echo '<span class="label label-success">' . plugin_lang_get('totp_enabled') . '</span> ';
            echo '<form action="' . plugin_page('admin-disable-totp') . '" method="post" style="display:inline">';
            echo form_security_field('plugin_totp_admin_disable');
            echo '<input type="hidden" name="user_id" value="' . $t_user_id . '" />';
            echo '<input type="submit" value="' . plugin_lang_get('totp_admin_disable_button') . '" ';
            echo 'class="btn btn-danger btn-xs" onclick="return confirm(\'' . plugin_lang_get('totp_admin_disable_confirm') . '\')" />';
            echo '</form>';
        } else {
            echo '<span class="label label-default">' . plugin_lang_get('totp_not_enabled') . '</span>';
        }
        echo '</td>';
        echo '</tr>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    // Handle custom authentication
    function auth_user_flags($p_event_name, $p_args)
    {
        // Retrieve user arguments
        $t_username = $p_args['username'];
        $t_user_id = $p_args['user_id'];

        // If user does not exists (or is anonymous), let him go through standard authentication
        if (!$t_user_id || user_is_anonymous($t_user_id)) {
            return null;
        }

        if(!isUserTOTPConfigured($t_user_id)) {
            return null;
        }


        // If we reach this point, use our own authentication process.
        $t_flags = new AuthFlags();
        $t_flags->setCredentialsPage(helper_url_combine(plugin_page('login-totp', true), 'username=' . $t_username));
        return $t_flags;
    }
}
