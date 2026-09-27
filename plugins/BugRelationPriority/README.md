# MantisBT Bug Relation Priority

**Version 3.0.0** *Compatible with MantisBT 2.X*

**Author:** Cristobal Montenegro ([@cristobalmontenegro](https://github.com/cristobalmontenegro))

---

## 🇬🇧 English Documentation

### Description

This plugin adds **configurable columns** to the bug relations table on the bug view page. Out of the box it shows each related bug's *Priority* and *Resolution* next to the columns the MantisBT core already renders, and it can optionally add any custom field.

Configuration is global and lives at `plugin.php?page=BugRelationPriority/config`; only users with the Manager access level (or whatever `manage_plugin_threshold` allows) can open it.

### How It Works

1. The plugin hooks into `EVENT_LAYOUT_RESOURCES` and `EVENT_LAYOUT_PAGE_FOOTER` on the bug view page (`view.php`) only.
2. `build_plan()` calls `relationship_get_all()` to collect the related bugs, then filters each one through `filter_viewable()` with the `view_bug_threshold` of the related bug's own project.
3. All values for the active columns are fetched in a single query (`query_native()` for core fields, one bulk load for custom fields) — no per-row queries.
4. The resulting JSON plan is embedded in a hidden `<div id="brp-plan" data-plan="...">`.
5. `relationcolumns.js` reads the plan and rebuilds the table: it inserts a `<thead>`, adds one `<th>`/`<td>` per active column, drops the core columns the manager chose to hide, and keeps the last cell (which holds the *delete relationship* button) in place.

### Features

- **Configurable**: pick any combination of core fields (*Priority*, *Resolution*, *Category*, *Reporter*, *Updated*, *Created*) and custom fields, and reorder them.
- **Optional header row**: the core renders the relations table without a `<thead>`; the plugin can add one with a single checkbox.
- **Core columns can be hidden**: *Assigned to* and *Project* can be removed. *Client* (the summary) can never be hidden because it holds the delete button.
- **Permission-aware**: values are only emitted for related bugs the current user may view, checked per project.
- **Localized labels**: uses `get_enum_element()` for priority/resolution and `string_custom_field_value()` for custom fields.
- **Non-invasive**: no core file changes, no database schema changes, no AJAX round-trips.
- **Fallback value**: shows *N/D* when a related bug has no value in that column.

### Installation

1. Upload the `BugRelationPriority` folder to the `plugins/` directory of your MantisBT installation.
2. Go to **Manage > Manage Plugins** and click **Install** on the plugin.
3. Open `plugin.php?page=BugRelationPriority/config` to choose the columns.

Upgrading from 2.x requires no schema migration: the new settings fall back to their defaults (`Priority` + `Resolution`, header shown) until the configuration page is saved.

### Requirements

- **MantisBT**: Version 2.0.0 or higher.

---

## 🇪🇸 Documentación en Español

### Descripción

Este plugin agrega **columnas configurables** a la tabla de relaciones de la página de visualización de casos. Por defecto muestra el *Tipo de Juicio* (prioridad) y la *Resolución* de cada caso relacionado, junto a las columnas que el core ya dibuja, y opcionalmente cualquier campo personalizado.

La configuración es global y está en `plugin.php?page=BugRelationPriority/config`. Solo los usuarios con nivel de acceso Manager (o el que permita `manage_plugin_threshold`) pueden abrirla.

### Cómo Funciona

1. El plugin se engancha a `EVENT_LAYOUT_RESOURCES` y `EVENT_LAYOUT_PAGE_FOOTER`, únicamente en la página de visualización del caso (`view.php`).
2. `build_plan()` llama a `relationship_get_all()` para reunir los casos relacionados y luego pasa cada uno por `filter_viewable()` usando el `view_bug_threshold` del proyecto al que pertenece el relacionado.
3. Los valores de todas las columnas activas se obtienen en una sola consulta (`query_native()` para campos del core y una carga masiva para los personalizados); no hay consultas por fila.
4. El plan resultante se incrusta como JSON en un `<div id="brp-plan" data-plan="...">` oculto.
5. `relationcolumns.js` lee el plan y reconstruye la tabla: inserta un `<thead>`, agrega un `<th>`/`<td>` por columna activa, elimina las columnas del core que el administrador decidió ocultar y mantiene en su lugar la última celda, que contiene el botón de borrar la relación.

### Configuración por defecto

| # | Columna | Origen |
|---|---------|--------|
| 1 | Relación | core |
| 2 | Juicio # | core |
| 3 | Estado | core |
| 4 | Asignada a | core |
| 5 | Provincia/Bufete | core (solo si la relación cruza proyectos) |
| 6 | Tipo de Juicio | campo `priority` |
| 7 | Resolución | campo `resolution` |
| 8 | Cliente | core (contiene el botón de borrar) |

### Características

- **Configurable**: combina cualquier grupo de campos del core (*Tipo de Juicio*, *Resolución*, *Severidad*, *Unidad/Competencia*, *Abogado Informante*, *Última actualización*, *Fecha de creación*), las **Etiquetas** del caso, su **Description** y cualquier campo personalizado, y reordénalos con los botones Subir/Bajar.
- **Dos secciones en la configuración**: *Campos del sistema* y *Campos personalizados*. La numeración del orden es por sección, para que no salgan huecos.
- **Encabezado opcional**: el core dibuja la tabla sin cabecera; el plugin puede agregar una con un solo checkbox.
- **Ocultar columnas del core**: se pueden quitar *Estado*, *Asignada a* y *Provincia/Bufete*. *Relación*, *Juicio #* y *Cliente* no se pueden ocultar: los dos primeros son la identidad de la fila y *Cliente* contiene el botón de borrar.
- **Etiquetas y Description**: se obtienen con la API del core (`tag_bug_get_attached()` y `bug_get_text_field()`). La Description se recorta a 120 caracteres y se le quitan las etiquetas HTML para que no descuadre la tabla.
- **Respetuoso con permisos**: solo se emiten valores de casos relacionados que el usuario puede ver, validando proyecto por proyecto.
- **Etiquetas localizadas**: usa `get_enum_element()` para tipo de juicio, resolución y severidad, y `string_custom_field_value()` para los personalizados.
- **No invasivo**: sin cambios en el core, sin cambios de esquema de base de datos y sin llamadas AJAX.
- **Valor de respaldo**: muestra *N/D* cuando el caso relacionado no tiene valor en esa columna.

### Instalación

1. Sube la carpeta `BugRelationPriority` al directorio `plugins/` de tu instalación de MantisBT.
2. Ve a **Administración > Administrar Plugins** y haz clic en **Instalar**.
3. Entra a `plugin.php?page=BugRelationPriority/config` para elegir las columnas.

**Al actualizar desde 2.x:** sustituye la carpeta completa del plugin. Se borra `files/priority.js`, que ya no se usa, y se agregan los directorios `lang/` y `pages/`. No requiere migración de base de datos ni reinstalar el plugin: los ajustes nuevos toman sus valores por defecto (*Tipo de Juicio* + *Resolución*, con encabezado) hasta que se guarde la página de configuración.

Si el navegador muestra la versión anterior, haz una recarga forzada (Ctrl+F5): la URL del JS no cambia entre versiones.

### Requisitos

- **MantisBT**: Versión 2.0.0 o superior.

---

### Change Log / Historial de Cambios

-   **3.0.0:** Columnas configurables con orden, encabezado opcional, ocultamiento de columnas del core, posición de inserción, texto para valores vacíos y página de configuración para Manager. Añade *Severidad*, *Etiquetas* y *Description* a las columnas disponibles, y organiza la configuración en dos secciones (*Campos del sistema* / *Campos personalizados*). Consulta única para todos los relacionados.
-   **2.0.3:** Security hardening — permission checks, input validation, XSS prevention via `htmlspecialchars()`.
-   **2.0.2:** Added `bug_exists()` validation for related bugs.
-   **2.0.1:** Added page-scoped injection (only activates on `view.php`).
-   **2.0.0:** Initial release with PHP data injection and JS DOM manipulation.

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
