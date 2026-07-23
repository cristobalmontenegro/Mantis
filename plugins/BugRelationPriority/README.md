# MantisBT Bug Relation Priority

**Version 2.0.3** *Compatible with MantisBT 2.X*

**Author:** Cristobal Montenegro ([@cristobalmontenegro](https://github.com/cristobalmontenegro))

---

## 🇬🇧 English Documentation

### Description

This plugin adds a **Priority** column to the bug relations table on the bug view page. When viewing an issue that has related bugs (via `related issues`), the plugin displays each related bug's priority level directly in the relationships table, making it easier to assess the importance of linked issues at a glance.

### How It Works

1. The plugin hooks into `EVENT_LAYOUT_RESOURCES` and `EVENT_LAYOUT_PAGE_FOOTER` on the bug view page (`view.php`).
2. On page load, a PHP script queries the database for all bugs related to the current issue (both source and destination relationships).
3. For each related bug, the plugin verifies the current user has permission to view it (`view_bug_threshold`) before including its priority.
4. Priority data is passed to the client via a hidden `<div>` with a JSON `data-priorities` attribute.
5. A JavaScript file (`priority.js`) reads this data and dynamically inserts a new **Priority** column into the relations HTML table, positioned just before the last column.

### Features

- **Permission-aware**: Only shows priority data for related bugs the current user is allowed to view.
- **Localized priority names**: Uses MantisBT's `get_enum_element()` to display priority labels in the user's configured language.
- **Non-invasive**: Injects data via a hidden element and JS DOM manipulation — no core file modifications required.
- **Fallback value**: Displays "N/D" (No Data) when priority information is unavailable.

### Installation

1. Upload the `BugRelationPriority` folder to the `plugins/` directory of your MantisBT installation.
2. Go to **Manage > Manage Plugins** in your MantisBT interface.
3. Find **Prioridad en Relaciones** in the list of Available Plugins and click **Install**.

### Requirements

- **MantisBT**: Version 2.0.0 or higher.

---

## 🇪🇸 Documentación en Español

### Descripción

Este plugin agrega una columna de **Prioridad** a la tabla de relaciones en la página de visualización de casos. Al ver un caso que tiene errores relacionados (a través de `casos relacionados`), el plugin muestra el nivel de prioridad de cada caso relacionado directamente en la tabla de relaciones, facilitando la evaluación de la importancia de los casos vinculados de un vistazo.

### Cómo Funciona

1. El plugin se engancha a `EVENT_LAYOUT_RESOURCES` y `EVENT_LAYOUT_PAGE_FOOTER` en la página de visualización del caso (`view.php`).
2. Al cargar la página, un script PHP consulta la base de datos para obtener todos los casos relacionados con el caso actual (tanto relaciones de origen como de destino).
3. Para cada caso relacionado, el plugin verifica que el usuario actual tenga permiso para verlo (`view_bug_threshold`) antes de incluir su prioridad.
4. Los datos de prioridad se pasan al cliente a través de un `<div>` oculto con un atributo JSON `data-priorities`.
5. Un archivo JavaScript (`priority.js`) lee estos datos e inserta dinámicamente una columna de **Prioridad** en la tabla HTML de relaciones, posicionada justo antes de la última columna.

### Características

- **Respetuoso con permisos**: Solo muestra datos de prioridad para casos relacionados que el usuario tiene permiso de ver.
- **Nombres de prioridad localizados**: Usa `get_enum_element()` de MantisBT para mostrar las etiquetas de prioridad en el idioma configurado por el usuario.
- **No invasivo**: Inyecta datos a través de un elemento oculto y manipulación del DOM con JS — no requiere modificaciones en archivos centrales de MantisBT.
- **Valor de respaldo**: Muestra "N/D" (Sin Datos) cuando la información de prioridad no está disponible.

### Instalación

1. Sube la carpeta `BugRelationPriority` al directorio `plugins/` de tu instalación de MantisBT.
2. Ve a **Administración > Administrar Plugins** en tu interfaz de MantisBT.
3. Encuentra **Prioridad en Relaciones** en la lista de Plugins Disponibles y haz clic en **Instalar**.

### Requisitos

- **MantisBT**: Versión 2.0.0 o superior.

---

### Change Log / Historial de Cambios

-   **2.0.3:** Security hardening — permission checks, input validation, XSS prevention via `htmlspecialchars()`.
-   **2.0.2:** Added `bug_exists()` validation for related bugs.
-   **2.0.1:** Added page-scoped injection (only activates on `view.php`).
-   **2.0.0:** Initial release with PHP data injection and JS DOM manipulation.

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
