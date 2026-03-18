<?php
define( 'PLUGIN_CR_PARAM_PERIOD_START', ':period_start' );
define( 'PLUGIN_CR_PARAM_PERIOD_END', ':period_end' );

function custom_reports_get_all() {
    $t_reports_table = plugin_table( 'reports' );
    $t_query = "SELECT * FROM $t_reports_table";
    $t_result = db_query( $t_query );
    $t_array = array();
    while ( $row = db_fetch_array( $t_result ) ) {
        $t_array[$row['id']] = $row;
    }
    return $t_array;
}

if ( !function_exists( 'strtotime_safe' ) ) {
    function strtotime_safe( $p_date, $p_allow_null = false ) {
        if( !$p_allow_null && ( $p_date == null || is_blank ( $p_date ) || $p_date === 1 ) ) {
            return date_get_null();
        }
        # Normalizamos la fecha para PHP
        $t_date = str_replace( '/', '-', $p_date );
        return strtotime( $t_date );
    }
}

if ( !function_exists( 'first_day_of_month' ) ) {
    function first_day_of_month( $p_add_months = 0, $p_format = null ) {
        if ( $p_format == null ) { $p_format = config_get( 'short_date_format' ); }
        return date( $p_format, mktime( 0, 0, 0, date( 'm' ) + $p_add_months, 1 ) );
    }
}

if ( !function_exists( 'last_day_of_month' ) ) {
    function last_day_of_month( $p_add_months = 0, $p_format = null ) {
        if ( $p_format == null ) { $p_format = config_get( 'short_date_format' ); }
        return date( $p_format, mktime( 0, 0, 0, date( 'm' ) + $p_add_months + 1, 0 ) );
    }
}