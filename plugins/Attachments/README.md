# MantisBT Plugin Attachments

**Version 3.2** *Compatible with MantisBT 2.X (Verified 2.28)*

**Credits:** Based on the 2021-2024 plugin by [Cas Nuy](http://www.NUY.info). **Maintained by:** Cristobal Montenegro

---

## 🇬🇧 English Documentation

### Description

This plugin allows you to re-instate the dedicated attachments section in your Mantis Bug Tracker issues. Normally, MantisBT handles attachments as part of bug notes or within the main description. This plugin provides a streamlined area specifically for uploading attachments *without* requiring an accompanying note, making document management faster and more intuitive.

Please note: Attachments uploaded this way are not shown in the activities section by default, although they appear in their own dedicated visual block just below the activities section when viewing an issue.

### Features

- **Dedicated upload form**: Upload files directly from the bug view page without creating a note.
- **PDF-only mode**: Optionally restrict uploads to PDF files only (configurable).
- **Customized layout**: Choose between standalone block or integrated row in the native details table.
- **Upload date display**: Shows the upload date for each attachment.
- **JS auto-positioning**: Automatically positions the upload form near the relationships section.
- **Security**: CSRF token validation, MIME type checking, file extension validation (allowed/disallowed lists), and XSS prevention.

### Installation

1. Upload the `Attachments` folder to the `plugins/` directory of your MantisBT installation.
2. Go to **Manage > Manage Plugins** in your MantisBT interface.
3. Find **Attachments** in the list of Available Plugins and click **Install**.

### Configuration

After installation, click on the plugin name in **Manage Plugins** to access configuration:

-   **Customized Setup** (`default OFF`): When `ON`, the upload form integrates as a row in the native bug details table. When `OFF` (default), it appears as a standalone block.
-   **PDF Only** (`default ON`): When `ON`, only PDF files can be uploaded through the plugin form. When `OFF`, any file type allowed by MantisBT's configuration can be uploaded.

### Customizing

In case you want to show the Attachments section just below the main section of the issue (as it used to be), you must make a change to a core script. **Be aware that such changes need to be applied again after each Mantis version update.**

To do this, modify `bug_view_inc.php`. Add the following line: `event_signal( 'EVENT_VIEW_BUG_FILES', array( $f_issue_id ) );` just before the comment: `# User list sponsoring the bug`

If you need the Attachment section in the `bug_update_page`, add the following line: `event_signal( 'EVENT_VIEW_BUG_FILES', array( $t_bug_id ) );` Just above this line: `define( 'BUGNOTE_VIEW_INC_ALLOW', true );`

**Excluding top-level attachments from the Activity list:** Add the following lines in `bugnote_view_inc.php`:

```php
# ATTACHMENTS-Plugin
if ( $t_activity['type'] == "attachment" ){
    continue;
}
# ATTACHMENTS-Plugin
```

These lines need to be inserted directly after the following code snippet:

```php
for( $i=0; $i < $t_activities_count; $i++ ) {
    $t_activity = $t_activities[$i];
```

---

## 🇪🇸 Documentación en Español

### Descripción

Este plugin te permite reinstaurar la sección dedicada de adjuntos en los casos de tu Mantis Bug Tracker. Normalmente, MantisBT maneja los adjuntos como parte de las notas o dentro de la descripción principal. Este plugin proporciona un área optimizada específicamente para subir archivos adjuntos *sin* necesidad de escribir una nota de acompañamiento, haciendo que la gestión de documentos sea más rápida e intuitiva.

Ten en cuenta que: Los adjuntos subidos de esta manera no se muestran en la sección de actividades por defecto, pero aparecerán en su propio bloque visual dedicado justo debajo de la sección de actividades cuando visualices un caso.

### Características

- **Formulario de subida dedicado**: Sube archivos directamente desde la página de visualización del caso sin crear una nota.
- **Modo solo PDF**: Opcionalmente restringe las subidas a solo archivos PDF (configurable).
- **Diseño personalizado**: Elige entre un bloque independiente o una fila integrada en la tabla nativa de detalles.
- **Fecha de carga**: Muestra la fecha de subida para cada adjunto.
- **Posicionamiento automático con JS**: Posiciona automáticamente el formulario de subida cerca de la sección de relaciones.
- **Seguridad**: Validación de token CSRF, verificación de tipo MIME, validación de extensiones de archivo (listas de permitidos/prohibidos) y prevención de XSS.

### Instalación

1. Sube la carpeta `Attachments` al directorio `plugins/` de tu instalación de MantisBT.
2. Ve a **Administración > Administrar Plugins** en tu interfaz de MantisBT.
3. Encuentra **Attachments** en la lista de Plugins Disponibles y haz clic en **Instalar**.

### Configuración

Después de la instalación, haz clic en el nombre del plugin en **Administrar Plugins** para acceder a la configuración:

-   **Customized Setup** (`por defecto OFF`): Cuando está en `ON`, el formulario de subida se integra como una fila en la tabla nativa de detalles del caso. Cuando está en `OFF` (por defecto), aparece como un bloque independiente.
-   **PDF Only** (`por defecto ON`): Cuando está en `ON`, solo se permiten archivos PDF a través del formulario del plugin. Cuando está en `OFF`, se permiten cualquier tipo de archivo configurado en MantisBT.

### Personalización

En caso de que quieras mostrar la sección de Adjuntos justo debajo de la sección principal del caso (como solía ser), debes hacer un cambio en un script principal (core). **Ten en cuenta que estos cambios deben aplicarse de nuevo después de cada actualización de versión de Mantis.**

Para hacer esto, modifica `bug_view_inc.php`. Agrega la siguiente línea: `event_signal( 'EVENT_VIEW_BUG_FILES', array( $f_issue_id ) );` justo antes del comentario: `# User list sponsoring the bug`

Si necesitas la sección de Adjuntos en la página de actualización `bug_update_page`, agrega la siguiente línea: `event_signal( 'EVENT_VIEW_BUG_FILES', array( $t_bug_id ) );` Justo por encima de esta línea: `define( 'BUGNOTE_VIEW_INC_ALLOW', true );`

**Excluir adjuntos de nivel superior de la lista de Actividades:** Agrega las siguientes líneas en `bugnote_view_inc.php`:

```php
# ATTACHMENTS-Plugin
if ( $t_activity['type'] == "attachment" ){
    continue;
}
# ATTACHMENTS-Plugin
```

Estas líneas deben insertarse directamente después del siguiente fragmento de código:

```php
for( $i=0; $i < $t_activities_count; $i++ ) {
    $t_activity = $t_activities[$i];
```

---

### Change Log / Historial de Cambios

-   **3.2:** Merged security hardening (CSRF, MIME checks, extension validation) with PDF-only config option. Replaced hardcoded Spanish strings with lang strings.
-   **3.1:** Added upload date display for each attachment using EVENT_VIEW_BUG_ATTACHMENT hook.
-   **3.0:** Verified 2.2X compatibility (tested on 2.28), bug fixes.
-   **2.25:** Added config option and improved readme.
-   **2.20:** Made this section available while editing an issue.
-   **2.10:** Added removed mantis function "function print_bug_attachments_list".
-   **2.04:** Fixed impact on alignment on following blocks.
-   **2.03:** Fixed not showing the top-level attachment in the activity section.
-   **2.02:** Adjusted the layout of the Attachments block slightly. Included an option to exclude the top-level attachments from the activity list.

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
