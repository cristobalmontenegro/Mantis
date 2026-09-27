<?php
/**
 * BugRelationPriority - Columnas configurables en la tabla de Relaciones.
 *
 * Agrega columnas a la tabla de relaciones de la vista de detalle de un caso y
 * permite ocultar columnas del core. No modifica el core ni la base de datos.
 *
 * La tabla de relaciones la genera el core SIN cabecera y con una anatomia
 * variable: la columna de proyecto solo existe cuando las relaciones cruzan
 * proyectos (bug_view_inc.php). Por eso el PHP no intenta "olfatear" el HTML:
 * replica la misma llamada que el core (relationship_get_all) y le entrega al
 * JS un plan explicito con la estructura real de columnas de esa pagina.
 */

class BugRelationPriorityPlugin extends MantisPlugin {

	/**
	 * Columnas del core, en el orden exacto en que bug_view_inc.php las emite.
	 * 'summary' es la ultima y nunca se oculta: contiene el boton de borrar.
	 * 'project' es condicional (solo si las relaciones cruzan proyectos).
	 */
	const CORE_COLUMNS = array( 'rel', 'id', 'status', 'handler', 'project', 'summary' );

	/**
	 * Columnas reales de {bug} para cada campo nativo. Ojo: en esta version
	 * priority y resolution NO llevan sufijo _id, y la fecha de creacion es
	 * date_submitted (no existe 'created').
	 */
	const NATIVE_DB_COLUMNS = array(
		'priority'   => 'priority',
		'resolution' => 'resolution',
		'severity'   => 'severity',
		'category'   => 'category_id',
		'reporter'   => 'reporter_id',
		'updated'    => 'last_updated',
		'created'    => 'date_submitted',
	);

	/**
	 * Columnas del core que el administrador puede ocultar. 'summary' nunca se
	 * oculta porque contiene el boton de borrar, y 'rel'/'id' quedan fijas
	 * porque son la identidad de la fila.
	 */
	const HIDEABLE_CORE = array( 'status', 'handler', 'project' );

	/** Campos nativos de bug ofrecidos como columna adicional => token. */
	const NATIVE_SOURCES = array(
		'priority'   => 'native:priority',
		'resolution' => 'native:resolution',
		'severity'   => 'native:severity',
		'category'   => 'native:category',
		'reporter'   => 'native:reporter',
		'updated'    => 'native:updated',
		'created'    => 'native:created',
	);

	/**
	 * Etiquetas de Mantis. No son un campo personalizado: viven en
	 * {bug_tag}/{tag}, asi que necesitan su propio grupo en el catalogo.
	 */
	const TAG_SOURCES = array( 'tags' => 'tags' );

	/**
	 * La Description vive en {bug_text}, no en {bug}. Se recorta para que una
	 * descripcion larga no rompa la tabla.
	 */
	const DESCRIPTION_MAX_LENGTH = 120;

	function register() {
		$this->name        = 'Columnas en Relaciones';
		$this->description = 'Agrega columnas configurables (Tipo de Juicio y campos personalizados) a la tabla de relaciones, con encabezado, y permite ocultar columnas del core.';
		$this->version     = '3.0.0';
		$this->requires    = array( 'MantisCore' => '2.0.0' );
		$this->author      = 'Cristobal Montenegro';
		$this->contact     = '';
		$this->url         = 'https://github.com/cristobalmontenegro';
		$this->page        = 'config';
	}

	function config() {
		return array(
			'columns'            => 'native:priority,native:resolution',
			'hidden_core'        => '',
			'show_header'        => ON,
			'empty_text'         => 'N/D',
			'insert_before_last' => ON,
		);
	}

	function install() {
		return true;
	}

	/**
	 * Normaliza tokens de versiones previas. La v2 no era configurable, pero
	 * si alguien dejo escrito 'priority' a mano, se lleva al token con
	 * namespace en vez de perder esa columna.
	 */
	function normalize_token( $p_token ) {
		return $p_token === 'priority' ? 'native:priority' : $p_token;
	}

	function hooks() {
		return array(
			'EVENT_LAYOUT_RESOURCES'   => 'inject_assets',
			'EVENT_LAYOUT_PAGE_FOOTER' => 'inject_plan',
		);
	}

	# ---------------------------------------------------------------------------
	# Config
	# ---------------------------------------------------------------------------

	/**
	 * Columnas adicionales activas, en orden de visualizacion.
	 * @return string[]
	 */
	function get_columns() {
		/* El segundo argumento es imprescindible: config() solo fija el valor
		 * global durante la instalacion, y un plugin ya instalado sin fila en
		 * config devolveria cadena vacia (y se perderia la columna por defecto). */
		return $this->parse_list( plugin_config_get( 'columns', 'native:priority,native:resolution' ) );
	}

	/**
	 * @return string[]
	 */
	function get_hidden_core() {
		return array_values( array_intersect(
			$this->parse_list( plugin_config_get( 'hidden_core', '' ) ),
			self::HIDEABLE_CORE
		) );
	}

	function show_header() {
		return plugin_config_get( 'show_header', ON ) == ON;
	}

	function get_empty_text() {
		$t_text = trim( (string)plugin_config_get( 'empty_text', 'N/D' ) );
		return $t_text === '' ? '-' : $t_text;
	}

	function insert_before_last() {
		return plugin_config_get( 'insert_before_last', ON ) == ON;
	}

	/**
	 * Divide una lista separada por comas en tokens saneados y sin duplicados.
	 * @return string[]
	 */
	function parse_list( $p_value ) {
		$t_result = array();
		if( $p_value === null || $p_value === '' ) {
			return $t_result;
		}
		foreach( explode( ',', (string)$p_value ) as $t_token ) {
			$t_token = $this->normalize_token( trim( $t_token ) );
			if( $t_token !== '' && !in_array( $t_token, $t_result, true ) ) {
				$t_result[] = $t_token;
			}
		}
		return $t_result;
	}

	# ---------------------------------------------------------------------------
	# Rotulos
	# ---------------------------------------------------------------------------

	/**
	 * Rotulos de las columnas del core. Se definen en el lang del plugin para
	 * no depender de claves que cambian entre versiones de Mantis, y para
	 * usar la terminologia legal del cliente.
	 * @return array
	 */
	function get_core_labels() {
		return array(
			'rel'     => plugin_lang_get( 'col_rel' ),
			'id'      => plugin_lang_get( 'col_id' ),
			'status'  => plugin_lang_get( 'col_status' ),
			'handler' => plugin_lang_get( 'col_handler' ),
			'project' => plugin_lang_get( 'col_project' ),
			'summary' => plugin_lang_get( 'col_summary' ),
		);
	}

	/**
	 * Rotulos de las columnas nativas adicionales.
	 * @return array source => texto
	 */
	function get_native_labels() {
		return array(
			'priority'   => plugin_lang_get( 'col_priority' ),
			'resolution' => plugin_lang_get( 'col_resolution' ),
			'severity'   => plugin_lang_get( 'col_severity' ),
			'category'   => plugin_lang_get( 'col_category' ),
			'reporter'   => plugin_lang_get( 'col_reporter' ),
			'updated'    => plugin_lang_get( 'col_updated' ),
			'created'    => plugin_lang_get( 'col_created' ),
		);
	}

	# ---------------------------------------------------------------------------
	# Catalogo de columnas disponibles
	# ---------------------------------------------------------------------------

	/**
	 * Todas las columnas adicionales que el administrador puede elegir,
	 * indexadas por token.
	 * @return array token => array( label, group, source )
	 */
	function get_available_extras() {
		$t_catalog = array();

		foreach( self::NATIVE_SOURCES as $t_source => $t_token ) {
			$t_catalog[$t_token] = array(
				'label'  => $this->get_native_labels()[$t_source],
				'group'  => 'native',
				'source' => $t_source,
			);
		}

		foreach( self::TAG_SOURCES as $t_source => $t_token ) {
			$t_catalog[$t_token] = array(
				'label'  => plugin_lang_get( 'col_tags' ),
				'group'  => 'tag',
				'source' => $t_source,
			);
		}

		/* La Description no tiene columna propia en {bug}: se resuelve con
		 * bug_get_field( $id, 'description' ), igual que hace el core. */
		$t_catalog['description'] = array(
			'label'  => plugin_lang_get( 'col_description' ),
			'group'  => 'native',
			'source' => 'description',
		);

		foreach( $this->get_custom_field_tokens() as $t_token => $t_field ) {
			$t_catalog[$t_token] = array(
				'label'  => $t_field['name'],
				'group'  => 'custom',
				'source' => $t_field['id'],
			);
		}

		return $t_catalog;
	}

	/**
	 * Custom fields del sistema, como token => definicion.
	 * @return array
	 */
	function get_custom_field_tokens() {
		$t_result = array();
		foreach( custom_field_get_ids() as $t_field_id ) {
			$t_definition = custom_field_get_definition( $t_field_id );
			if( empty( $t_definition['name'] ) ) {
				continue;
			}
			$t_result[ 'cf:' . (int)$t_field_id ] = array(
				'id'   => (int)$t_field_id,
				/* Texto CRUDO a proposito: esta etiqueta se imprime en dos
				 * sitios distintos, con escapado distinto. En pages/config.php
				 * se escapa con string_display() al pintarla en el HTML, y en
				 * la tabla de relaciones la pinta el JS con textContent. Si se
				 * escapara aqui, se veria "Barrios &amp; Cia" en ambos. */
				'name' => $t_definition['name'],
			);
		}
		return $t_result;
	}

	# ---------------------------------------------------------------------------
	# Inyeccion en la pagina
	# ---------------------------------------------------------------------------

	/**
	 * @return int id del caso en la pagina de detalle, o 0
	 */
	function get_viewed_bug_id() {
		if( basename( $_SERVER['SCRIPT_NAME'] ) !== 'view.php' ) {
			return 0;
		}
		return (int)gpc_get_int( 'id', 0 );
	}

	function inject_assets( $p_event ) {
		if( $this->get_viewed_bug_id() <= 0 ) {
			return '';
		}
		return '<link rel="stylesheet" href="'
			. string_attribute( plugin_file( 'relationcolumns.css' ) ) . '">'
			. '<script src="'
			. string_attribute( plugin_file( 'relationcolumns.js' ) ) . '"></script>';
	}

	/**
	 * Entrega al JS el plan de columnas y los valores de esa pagina.
	 */
	function inject_plan( $p_event ) {
		$t_bug_id = $this->get_viewed_bug_id();
		if( $t_bug_id <= 0 ) {
			return;
		}

		$t_plan = $this->build_plan( $t_bug_id );
		if( empty( $t_plan ) ) {
			return;
		}

		echo '<div id="brp-plan" data-plan="'
			. string_attribute( json_encode( $t_plan, JSON_UNESCAPED_UNICODE ) )
			. '" style="display:none;"></div>';
	}

	# ---------------------------------------------------------------------------
	# Construccion del plan
	# ---------------------------------------------------------------------------

	/**
	 * Replica la estructura que el core ya pinto en la tabla de relaciones y
	 * anexa los datos de las columnas adicionales configuradas.
	 *
	 * @param int $p_bug_id
	 * @return array|null
	 */
	function build_plan( $p_bug_id ) {
		if( !bug_exists( $p_bug_id ) ) {
			return null;
		}

		$t_catalog = $this->get_available_extras();
		$t_columns = array_values( array_intersect( $this->get_columns(), array_keys( $t_catalog ) ) );
		$t_hidden = $this->get_hidden_core();

		if( empty( $t_columns ) && empty( $t_hidden ) ) {
			return null;
		}

		# Misma llamada que hace el core: define si aparece la columna de
		# proyecto y devuelve las relaciones en el mismo orden.
		$t_show_project = false;
		$t_relationships = relationship_get_all( $p_bug_id, $t_show_project );

		$t_related_ids = $this->collect_related_ids( $p_bug_id, $t_relationships );
		if( empty( $t_related_ids ) ) {
			return null;
		}

		# Solo los casos que el usuario puede ver, con el mismo criterio del
		# core (bug_view_inc.php): umbral de lectura POR PROYECTO.
		$t_visible_ids = $this->filter_viewable( $t_related_ids );
		if( empty( $t_visible_ids ) ) {
			return null;
		}

		$t_core_columns = self::CORE_COLUMNS;
		if( !$t_show_project ) {
			$t_core_columns = array_values( array_diff( $t_core_columns, array( 'project' ) ) );
		}

		$t_extras = array();
		foreach( $t_columns as $t_token ) {
			$t_extras[] = array( 'key' => $t_token, 'label' => $t_catalog[$t_token]['label'] );
		}

		return array(
			'core'       => $t_core_columns,
			'labels'     => $this->get_core_labels(),
			'hidden'     => $t_hidden,
			'extras'     => $t_extras,
			'values'     => empty( $t_columns ) ? array() : $this->collect_values( $t_visible_ids, $t_columns, $t_catalog ),
			'showHeader' => $this->show_header(),
			'empty'      => $this->get_empty_text(),
			'beforeLast' => $this->insert_before_last(),
		);
	}

	/**
	 * Ids de los casos relacionados, deduplicados y en el orden del core.
	 * @return int[]
	 */
	function collect_related_ids( $p_bug_id, array $p_relationships ) {
		$t_ids = array();
		foreach( $p_relationships as $t_relationship ) {
			$t_related_id = ( $p_bug_id == $t_relationship->src_bug_id )
				? $t_relationship->dest_bug_id
				: $t_relationship->src_bug_id;
			$t_related_id = (int)$t_related_id;
			if( $t_related_id > 0 && $t_related_id != $p_bug_id && !in_array( $t_related_id, $t_ids, true ) ) {
				$t_ids[] = $t_related_id;
			}
		}
		return $t_ids;
	}

	/**
	 * Aplica el chequeo de acceso que el core ya aplico al pintar la fila, de
	 * modo que el plugin nunca exponga el valor de un caso invisible.
	 *
	 * @param int[] $p_ids
	 * @return int[]
	 */
	function filter_viewable( array $p_ids ) {
		$t_result = array();
		foreach( $p_ids as $t_id ) {
			if( !bug_exists( $t_id ) ) {
				continue;
			}
			$t_project_id = bug_get_field( $t_id, 'project_id' );
			if( access_has_bug_level( config_get( 'view_bug_threshold', null, null, $t_project_id ), $t_id ) ) {
				$t_result[] = $t_id;
			}
		}
		return $t_result;
	}

	# ---------------------------------------------------------------------------
	# Lectura de valores
	# ---------------------------------------------------------------------------

	/**
	 * Lee los valores de todas las columnas adicionales con una sola query por
	 * tipo de dato (nativos / custom fields), no una por fila.
	 *
	 * @param int[]    $p_bug_ids
	 * @param string[] $p_columns
	 * @param array    $p_catalog
	 * @return array bug_id => array token => texto
	 */
	function collect_values( array $p_bug_ids, array $p_columns, array $p_catalog ) {
		$t_native_tokens = array();
		$t_custom_tokens = array();
		$t_tag_tokens = array();
		$t_description_tokens = array();
		foreach( $p_columns as $t_token ) {
			switch( $p_catalog[$t_token]['group'] ) {
				case 'custom':
					$t_custom_tokens[] = $t_token;
					break;
				case 'tag':
					$t_tag_tokens[] = $t_token;
					break;
				default:
					if( $p_catalog[$t_token]['source'] === 'description' ) {
						$t_description_tokens[] = $t_token;
					} else {
						$t_native_tokens[] = $t_token;
					}
					break;
			}
		}

		$t_values = array();

		if( !empty( $t_native_tokens ) ) {
			foreach( $this->query_native( $p_bug_ids, $t_native_tokens, $p_catalog ) as $t_bug_id => $t_row ) {
				$t_values[$t_bug_id] = $this->format_native_row( $t_bug_id, $t_row, $t_native_tokens, $p_catalog );
			}
		}

		if( !empty( $t_custom_tokens ) ) {
			$this->collect_custom_values( $p_bug_ids, $t_custom_tokens, $p_catalog, $t_values );
		}

		if( !empty( $t_tag_tokens ) ) {
			$this->collect_tag_values( $p_bug_ids, $t_tag_tokens, $p_catalog, $t_values );
		}

		if( !empty( $t_description_tokens ) ) {
			$this->collect_description_values( $p_bug_ids, $t_description_tokens, $t_values );
		}

		return $t_values;
	}

	/**
	 * Una unica query para todos los casos relacionados: prioridad, resolucion,
	 * categoria, informante y fechas de una vez.
	 *
	 * @return array bug_id => fila
	 */
	function query_native( array $p_bug_ids, array $p_tokens, array $p_catalog ) {
		$t_select = array();
		foreach( $p_tokens as $t_token ) {
			$t_field = $p_catalog[$t_token]['source'];
			/* Whitelist explicita: este es el unico punto donde un nombre de
			 * columna entra al SQL sin comillas, asi que se comprueba contra
			 * la lista blanca en vez de confiar en que el catalogo este bien. */
			if( !isset( self::NATIVE_DB_COLUMNS[$t_field] ) ) {
				continue;
			}
			$t_column = self::NATIVE_DB_COLUMNS[$t_field];
			if( !in_array( $t_column, $t_select, true ) ) {
				$t_select[] = $t_column;
			}
		}
		if( empty( $t_select ) ) {
			return array();
		}

		$t_params = array();
		$t_id_params = array();
		foreach( $p_bug_ids as $t_bug_id ) {
			$t_id_params[] = db_param();
			$t_params[] = $t_bug_id;
		}

		$t_query = 'SELECT b.id, ' . implode( ', ', $t_select ) . ' FROM {bug} b'
			. ' WHERE b.id IN (' . implode( ',', $t_id_params ) . ')';
		$t_result = db_query( $t_query, $t_params );

		$t_rows = array();
		while( $t_row = db_fetch_array( $t_result ) ) {
			$t_rows[ (int)$t_row['id'] ] = $t_row;
		}
		return $t_rows;
	}

	/**
	 * Convierte una fila de {bug} al texto visible de cada columna nativa.
	 *
	 * @return array token => texto
	 */
	function format_native_row( $p_bug_id, array $p_row, array $p_tokens, array $p_catalog ) {
		$t_result = array();
		$t_user_id = auth_get_current_user_id();
		$t_project_id = bug_get_field( $p_bug_id, 'project_id' );
		$t_date_format = config_get( 'short_date_format' );

		foreach( $p_tokens as $t_token ) {
			$t_source = $p_catalog[$t_token]['source'];
			$t_column = self::NATIVE_DB_COLUMNS[$t_source];
			$t_raw = $p_row[$t_column];
			$t_text = '';

			/* Todo texto que va al plan JSON debe ir CRUDO: lo imprime el JS con
			 * textContent, que ya escapa. Escaparlo aqui (string_display)
			 * produciria doble escapado y se verian las entidades "&amp;". */
			switch( $t_source ) {
				case 'priority':
				case 'resolution':
				case 'severity':
					$t_text = (string)get_enum_element( $t_source, $t_raw, $t_user_id, $t_project_id );
					break;

				case 'category':
					$t_text = $t_raw > 0
						? category_get_name( $t_raw )
						: '';
					break;

				case 'reporter':
					$t_text = $t_raw > 0
						? prepare_user_name( $t_raw )
						: '';
					break;

				case 'updated':
					$t_text = $t_raw > 0
						? date( $t_date_format, $t_raw )
						: '';
					break;

				case 'created':
					$t_text = $t_raw > 0
						? date( $t_date_format, $t_raw )
						: '';
					break;
			}

			$t_text = trim( $t_text );
			$t_result[$t_token] = $t_text === '' ? $this->get_empty_text() : $t_text;
		}
		return $t_result;
	}

	/**
	 * Una sola query para todos los custom fields gracias a la cache de
	 * Mantis; custom_field_get_value() ademas valida el access_level_r de
	 * cada campo.
	 */
	function collect_custom_values( array $p_bug_ids, array $p_tokens, array $p_catalog, array &$p_values ) {
		$t_field_ids = array();
		foreach( $p_tokens as $t_token ) {
			$t_field_id = (int)$p_catalog[$t_token]['source'];
			if( $t_field_id > 0 && !in_array( $t_field_id, $t_field_ids, true ) ) {
				$t_field_ids[] = $t_field_id;
			}
		}
		if( empty( $t_field_ids ) ) {
			return;
		}

		custom_field_cache_values( $p_bug_ids, $t_field_ids );

		foreach( $p_bug_ids as $t_bug_id ) {
			foreach( $p_tokens as $t_token ) {
				$t_field_id = (int)$p_catalog[$t_token]['source'];
				$t_definition = custom_field_get_definition( $t_field_id );
				$t_text = trim( (string)string_custom_field_value( $t_definition, $t_field_id, $t_bug_id ) );
				if( !isset( $p_values[$t_bug_id] ) ) {
					$p_values[$t_bug_id] = array();
				}
				$p_values[$t_bug_id][$t_token] = $t_text === '' ? $this->get_empty_text() : $t_text;
			}
		}
	}

	/**
	 * Etiquetas del caso, con la API del core (tag_bug_get_attached ya usa la
	 * cache de {bug_tag}, asi que no hay una consulta por fila).
	 */
	function collect_tag_values( array $p_bug_ids, array $p_tokens, array $p_catalog, array &$p_values ) {
		foreach( $p_bug_ids as $t_bug_id ) {
			$t_names = array();
			foreach( tag_bug_get_attached( $t_bug_id ) as $t_tag ) {
				if( !empty( $t_tag['name'] ) ) {
					$t_names[] = (string)$t_tag['name'];
				}
			}
			/* Crudo: lo pinta el JS con textContent. Los nombres de etiqueta los
			 * crea cualquier usuario, asi que no pueden ir como HTML. */
			$t_text = trim( implode( ', ', $t_names ) );
			if( !isset( $p_values[$t_bug_id] ) ) {
				$p_values[$t_bug_id] = array();
			}
			foreach( $p_tokens as $t_token ) {
				$p_values[$t_bug_id][$t_token] = $t_text === '' ? $this->get_empty_text() : $t_text;
			}
		}
	}

	/**
	 * Description del caso. Se recorta y se le quitan las etiquetas HTML: puede
	 * traer marcado y ser muy larga, y en una tabla eso descuadra todo.
	 *
	 * OJO: la description NO esta en {bug}, vive en {bug_text}. Hay que usar
	 * bug_get_text_field(); bug_get_field( $id, 'description' ) devuelve ''.
	 */
	function collect_description_values( array $p_bug_ids, array $p_tokens, array &$p_values ) {
		foreach( $p_bug_ids as $t_bug_id ) {
			$t_text = (string)bug_get_text_field( $t_bug_id, 'description' );
			$t_text = trim( html_entity_decode( strip_tags( $t_text ), ENT_QUOTES, 'UTF-8' ) );
			$t_text = trim( preg_replace( '/\s+/u', ' ', $t_text ) );
			if( mb_strlen( $t_text ) > self::DESCRIPTION_MAX_LENGTH ) {
				$t_text = rtrim( mb_substr( $t_text, 0, self::DESCRIPTION_MAX_LENGTH ) ) . '...';
			}
			if( !isset( $p_values[$t_bug_id] ) ) {
				$p_values[$t_bug_id] = array();
			}
			foreach( $p_tokens as $t_token ) {
				$p_values[$t_bug_id][$t_token] = $t_text === '' ? $this->get_empty_text() : $t_text;
			}
		}
	}
}
