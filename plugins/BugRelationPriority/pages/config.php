<?php
/**
 * Pagina de configuracion: que columnas mostrar, en que orden, y que columnas
 * del core ocultar. Sin JavaScript: el orden de las columnas adicionales se
 * toma del orden en que llegan los checkbox marcados, y el reordenamiento se
 * hace con botones que reenvian el formulario.
 *
 * La lista se presenta en dos secciones, segun de donde viene el campo:
 * "Campos del sistema" (columnas del core mas las del sistema) y
 * "Campos personalizados". Asi no hace falta una columna "Origen".
 */
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

layout_page_header( plugin_lang_get( 'title' ) );
layout_page_begin( 'config.php' );
print_manage_menu();

$t_plugin = plugin_get( 'BugRelationPriority' );
$t_catalog = $t_plugin->get_available_extras();
$t_active = $t_plugin->get_columns();
$t_hidden = $t_plugin->get_hidden_core();
$t_core_labels = $t_plugin->get_core_labels();
$t_core_fields = BugRelationPriorityPlugin::CORE_COLUMNS;
$t_hideable = BugRelationPriorityPlugin::HIDEABLE_CORE;

/* Posicion de cada columna adicional activa DENTRO de su seccion. Numero por
 * seccion y no global: si no, con columnas activas en las dos tablas salen
 * huecos (1, 2, 4 arriba y 3 abajo) y no se entiende el orden. */
$t_positions = array();
foreach( $t_active as $t_token ) {
	if( !isset( $t_catalog[$t_token] ) ) {
		continue;
	}
	$t_group = $t_catalog[$t_token]['group'] == 'custom' ? 'custom' : 'system';
	if( !isset( $t_positions[$t_group] ) ) {
		$t_positions[$t_group] = array();
	}
	$t_positions[$t_group][$t_token] = count( $t_positions[$t_group] ) + 1;
}
$t_active_total = array(
	'system' => isset( $t_positions['system'] ) ? count( $t_positions['system'] ) : 0,
	'custom' => isset( $t_positions['custom'] ) ? count( $t_positions['custom'] ) : 0,
);

/* Las columnas del sistema y las personalizadas, separadas; dentro de cada
 * grupo, primero las ya activas en su orden configurado. */
$t_extras_system = array();
$t_extras_custom = array();
foreach( $t_catalog as $t_token => $t_def ) {
	if( $t_def['group'] == 'custom' ) {
		$t_extras_custom[] = $t_token;
	} else {
		$t_extras_system[] = $t_token;
	}
}
$t_ordered_system = array();
$t_ordered_custom = array();
foreach( $t_active as $t_token ) {
	if( !isset( $t_catalog[$t_token] ) ) {
		continue;
	}
	if( $t_catalog[$t_token]['group'] == 'custom' ) {
		$t_ordered_custom[] = $t_token;
	} else {
		$t_ordered_system[] = $t_token;
	}
}
foreach( $t_extras_system as $t_token ) {
	if( !in_array( $t_token, $t_ordered_system, true ) ) {
		$t_ordered_system[] = $t_token;
	}
}
foreach( $t_extras_custom as $t_token ) {
	if( !in_array( $t_token, $t_ordered_custom, true ) ) {
		$t_ordered_custom[] = $t_token;
	}
}

/* Seccion del sistema: se arma en el orden real de la tabla, es decir, las
 * columnas del core, las adicionales, y Cliente al final (siempre visible
 * porque contiene el boton de borrar la relacion). */
$t_rows_system = array();
foreach( $t_core_fields as $t_key ) {
	if( $t_key == 'summary' ) {
		foreach( $t_ordered_system as $t_token ) {
			$t_rows_system[] = array( 'kind' => 'extra', 'token' => $t_token );
		}
	}
	$t_rows_system[] = array( 'kind' => 'core', 'key' => $t_key );
}
$t_rows_custom = array();
foreach( $t_ordered_custom as $t_token ) {
	$t_rows_custom[] = array( 'kind' => 'extra', 'token' => $t_token );
}
?>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>
<div class="form-container">
<form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
<?php echo form_security_field( 'plugin_BugRelationPriority_config_edit' ) ?>

<div class="widget-box widget-color-blue2">
<div class="widget-header widget-header-small">
	<h4 class="widget-title lighter">
		<i class="ace-icon fa fa-table"></i>
		<?php echo plugin_lang_get( 'title' ) ?>
	</h4>
</div>
<div class="widget-body">
<div class="widget-main">

<div class="space-12"></div>

<?php
if( !function_exists( 'brp_print_section' ) ) {
/**
 * Imprime una tabla de seccion. Cada fila es del core (columna fija u
 * ocultable) o una columna adicional con su orden.
 * @param array $t_rows   Filas de la seccion.
 * @param array $t_active Columnas adicionales activas.
 * @param string $t_group 'system' o 'custom': seccion a la que pertenece.
 */
function brp_print_section( array $t_rows, array $t_active, array $t_positions, array $t_active_total, array $t_catalog, array $t_core_labels, array $t_hideable, array $t_hidden, $t_group ) {
	$t_pos = isset( $t_positions[$t_group] ) ? $t_positions[$t_group] : array();
	$t_total = $t_active_total[$t_group];
?>
	<div class="table-responsive">
	<table class="table table-bordered table-condensed table-striped">
		<tr>
			<th width="60"><?php echo plugin_lang_get( 'on' ) ?></th>
			<th><?php echo lang_get( 'name' ) ?></th>
			<th width="210"><?php echo plugin_lang_get( 'order' ) ?></th>
		</tr>
<?php
	foreach( $t_rows as $t_row ):
		$t_is_core = $t_row['kind'] == 'core';
		$t_key = $t_is_core ? $t_row['key'] : $t_row['token'];
		$t_label = $t_is_core
			? $t_core_labels[$t_key]
			: $t_catalog[$t_key]['label'];
		$t_hideable_core = $t_is_core && in_array( $t_key, $t_hideable, true );
		$t_is_checked = $t_is_core
			? !in_array( $t_key, $t_hidden, true )
			: in_array( $t_key, $t_active, true );
		$t_position = $t_is_core ? 0 : ( isset( $t_pos[$t_key] ) ? $t_pos[$t_key] : 0 );
?>
		<tr>
			<td>
<?php if( $t_is_core && !$t_hideable_core ): ?>
				<label>
					<input type="checkbox" class="ace" checked="checked" disabled="disabled" />
					<span class="lbl text-muted"><?php echo plugin_lang_get( 'always' ) ?></span>
				</label>
<?php elseif( $t_is_core ): ?>
				<label>
					<input type="checkbox" name="visible_core[]" value="<?php echo string_attribute( $t_key ) ?>"
						class="ace" <?php echo $t_is_checked ? 'checked="checked"' : '' ?> />
					<span class="lbl"><?php echo $t_is_checked ? plugin_lang_get( 'yes' ) : plugin_lang_get( 'no' ) ?></span>
				</label>
<?php else: ?>
				<label>
					<input type="checkbox" name="columns[]" value="<?php echo string_attribute( $t_key ) ?>"
						class="ace" <?php echo $t_is_checked ? 'checked="checked"' : '' ?> />
					<span class="lbl"><?php echo $t_is_checked ? plugin_lang_get( 'yes' ) : plugin_lang_get( 'no' ) ?></span>
				</label>
<?php endif ?>
			</td>
			<td><?php echo string_display( $t_label ) ?></td>
			<td>
<?php if( $t_is_core || $t_position == 0 ): ?>
				<span class="text-muted">-</span>
<?php else: ?>
				<span class="label label-primary"><?php echo $t_position ?></span>
				<button type="submit" name="move" value="up:<?php echo string_attribute( $t_key ) ?>"
					class="btn btn-mini btn-default" title="<?php echo plugin_lang_get( 'move_up' ) ?>"
					<?php echo $t_position == 1 ? 'disabled="disabled"' : '' ?>>
					<i class="ace-icon fa fa-arrow-up"></i>&nbsp;<?php echo plugin_lang_get( 'move_up' ) ?></button>
				<button type="submit" name="move" value="down:<?php echo string_attribute( $t_key ) ?>"
					class="btn btn-mini btn-default" title="<?php echo plugin_lang_get( 'move_down' ) ?>"
					<?php echo $t_position == $t_total ? 'disabled="disabled"' : '' ?>>
					<i class="ace-icon fa fa-arrow-down"></i>&nbsp;<?php echo plugin_lang_get( 'move_down' ) ?></button>
<?php endif ?>
			</td>
		</tr>
<?php endforeach ?>
	</table>
	</div>
<?php
}
}
?>

<h4><?php echo plugin_lang_get( 'section_system' ) ?></h4>
<p class="help-block"><?php echo plugin_lang_get( 'section_system_desc' ) ?></p>
<?php brp_print_section( $t_rows_system, $t_active, $t_positions, $t_active_total, $t_catalog, $t_core_labels, $t_hideable, $t_hidden, 'system' ) ?>

<div class="space-12"></div>
<h4><?php echo plugin_lang_get( 'section_custom' ) ?></h4>
<p class="help-block"><?php echo plugin_lang_get( 'section_custom_desc' ) ?></p>
<?php if( empty( $t_rows_custom ) ): ?>
	<div class="alert alert-warning"><?php echo plugin_lang_get( 'none_available' ) ?></div>
<?php else: ?>
	<?php brp_print_section( $t_rows_custom, $t_active, $t_positions, $t_active_total, $t_catalog, $t_core_labels, $t_hideable, $t_hidden, 'custom' ) ?>
<?php endif ?>

<div class="space-12"></div>
<div class="table-responsive">
<table class="table table-bordered table-condensed table-striped">
	<tr>
		<td class="category" width="50%"><?php echo plugin_lang_get( 'show_header' ) ?></td>
		<td width="50%">
			<label>
				<input type="checkbox" name="show_header" value="1" class="ace"
					<?php echo $t_plugin->show_header() ? 'checked="checked"' : '' ?>>
				<span class="lbl"><?php echo plugin_lang_get( 'show_header_checkbox' ) ?></span>
			</label>
			<p class="help-block"><?php echo plugin_lang_get( 'show_header_desc' ) ?></p>
		</td>
	</tr>
	<tr>
		<td class="category"><?php echo plugin_lang_get( 'position' ) ?></td>
		<td>
			<label>
				<input type="radio" name="insert_before_last" value="1" class="ace"
					<?php echo $t_plugin->insert_before_last() ? 'checked="checked"' : '' ?>>
				<span class="lbl"><?php echo plugin_lang_get( 'position_before' ) ?></span>
			</label>
			<label>
				<input type="radio" name="insert_before_last" value="0" class="ace"
					<?php echo !$t_plugin->insert_before_last() ? 'checked="checked"' : '' ?>>
				<span class="lbl"><?php echo plugin_lang_get( 'position_after' ) ?></span>
			</label>
			<p class="help-block"><?php echo plugin_lang_get( 'position_desc' ) ?></p>
		</td>
	</tr>
	<tr>
		<td class="category"><?php echo plugin_lang_get( 'empty_text' ) ?></td>
		<td>
			<input type="text" name="empty_text" class="input-sm" maxlength="20"
				value="<?php echo string_attribute( $t_plugin->get_empty_text() ) ?>" />
			<p class="help-block"><?php echo plugin_lang_get( 'empty_text_desc' ) ?></p>
		</td>
	</tr>
</table>
</div>

</div>
<div class="widget-toolbox padding-8 clearfix">
	<input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'change_configuration' ) ?>" />
</div>
</div>
</div>
</form>
</div>
</div>
<?php
layout_page_end();
