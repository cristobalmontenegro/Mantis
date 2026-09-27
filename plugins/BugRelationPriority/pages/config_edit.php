<?php
/**
 * Guardado de la configuracion de columnas.
 *
 * El orden de las columnas adicionales es el orden en que llegan los checkbox
 * marcados (la pagina los imprime en ese orden). Si se presiono un boton de
 * mover, se aplica el desplazamiento sobre esa lista.
 */
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

form_security_validate( 'plugin_BugRelationPriority_config_edit' );

$t_plugin = plugin_get( 'BugRelationPriority' );
$t_catalog = $t_plugin->get_available_extras();

/* --- Columnas adicionales: se ignoran tokens que no existan en el catalogo,
 *     de modo que un custom field borrado no rompa la pagina. --- */
$t_columns = array();
foreach( (array)gpc_get( 'columns', array() ) as $t_token ) {
	$t_token = trim( (string)$t_token );
	if( $t_token !== '' && isset( $t_catalog[$t_token] ) && !in_array( $t_token, $t_columns, true ) ) {
		$t_columns[] = $t_token;
	}
}

/* --- Reordenamiento solicitado por los botones ---
 * El desplazamiento se acota a la MISMA seccion (sistema o personalizados).
 * Si no, mover "Descripcion" podria meterla entre dos campos personalizados
 * y los numeros de cada tabla quedarian con huecos. */
$t_move = (string)gpc_get( 'move', '' );
if( $t_move !== '' && strpos( $t_move, ':' ) !== false ) {
	list( $t_direction, $t_token ) = explode( ':', $t_move, 2 );
	$t_index = array_search( $t_token, $t_columns, true );
	if( $t_index !== false && isset( $t_catalog[$t_token] ) ) {
		$t_group = $t_catalog[$t_token]['group'] == 'custom' ? 'custom' : 'system';
		$t_sames = array();
		foreach( $t_columns as $t_i => $t_candidate ) {
			$t_candidate_group = isset( $t_catalog[$t_candidate] ) && $t_catalog[$t_candidate]['group'] == 'custom'
				? 'custom'
				: 'system';
			if( $t_candidate_group === $t_group ) {
				$t_sames[] = $t_i;
			}
		}
		$t_slot = array_search( $t_index, $t_sames, true );
		if( $t_slot !== false ) {
			$t_neighbour = ( $t_direction === 'up' ) ? $t_slot - 1 : $t_slot + 1;
			if( $t_neighbour >= 0 && $t_neighbour < count( $t_sames ) ) {
				$t_from = $t_sames[$t_slot];
				$t_to = $t_sames[$t_neighbour];
				$t_moved = $t_columns[$t_from];
				unset( $t_columns[$t_from] );
				array_splice( $t_columns, $t_to, 0, array( $t_moved ) );
			}
		}
	}
}

plugin_config_set( 'columns', implode( ',', $t_columns ) );

/* --- Columnas del core a ocultar: el formulario envia las VISIBLES marcadas,
 *     asi que lo oculto es el complemento. 'summary' nunca se oculta. --- */
$t_hideable = BugRelationPriorityPlugin::HIDEABLE_CORE;
$t_visible_core = array();
foreach( (array)gpc_get( 'visible_core', array() ) as $t_key ) {
	$t_key = trim( (string)$t_key );
	if( in_array( $t_key, $t_hideable, true ) && !in_array( $t_key, $t_visible_core, true ) ) {
		$t_visible_core[] = $t_key;
	}
}
$t_hidden = array();
foreach( $t_hideable as $t_key ) {
	if( !in_array( $t_key, $t_visible_core, true ) ) {
		$t_hidden[] = $t_key;
	}
}
plugin_config_set( 'hidden_core', implode( ',', $t_hidden ) );

plugin_config_set( 'show_header', gpc_get_int( 'show_header', 0 ) == 1 ? ON : OFF );
plugin_config_set( 'insert_before_last', gpc_get_int( 'insert_before_last', 1 ) == 1 ? ON : OFF );

$t_empty = trim( (string)gpc_get( 'empty_text', '' ) );
if( mb_strlen( $t_empty ) > 20 ) {
	$t_empty = mb_substr( $t_empty, 0, 20 );
}
plugin_config_set( 'empty_text', $t_empty === '' ? '-' : $t_empty );

form_security_purge( 'plugin_BugRelationPriority_config_edit' );

print_header_redirect( 'plugin.php?page=BugRelationPriority/config' );
