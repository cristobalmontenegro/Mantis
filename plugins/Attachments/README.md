# MantisBT Plugin Attachments 
**Version 3.1**
*Compatible with MantisBT 2.X (Verified 2.28)*

**Credits:** Based on the 2021-2024 plugin by [Cas Nuy](http://www.NUY.info).
**Maintained by:** Cristobal Montenegro

---

## 🇬🇧 English Documentation

### Description	
This plugin allows you to re-instate the dedicated attachments section in your Mantis Bug Tracker issues. Normally, MantisBT handles attachments as part of bug notes or within the main description. This plugin provides a streamlined area specifically for uploading attachments *without* requiring an accompanying note, making document management faster and more intuitive.

Please note: Attachments uploaded this way are not shown in the activities section by default, although they appear in their own dedicated visual block just below the activities section when viewing an issue.

This version (3.1) adds upload date display for each attachment and ensures full compatibility with MantisBT 2.28 and modern 2.X environments.

### Installation
1. Upload the `Attachments` folder to the `plugins/` directory of your MantisBT installation.
2. Go to **Manage > Manage Plugins** in your MantisBT interface.
3. Find **Attachments** in the list of Available Plugins and click **Install**.

### Customizing
In case you want to show the Attachments section just below the main section of the issue (as it used to be), you must make a change to a core script.
**Be aware that such changes need to be applied again after each Mantis version update.**

To do this, modify `bug_view_inc.php`.
Add the following line:
`event_signal( 'EVENT_VIEW_BUG_FILES', array( $f_issue_id ) );`
just before the comment: `# User list sponsoring the bug`

If you need the Attachment section in the `bug_update_page`, add the following line:
`event_signal( 'EVENT_VIEW_BUG_FILES', array( $t_bug_id ) );` 
Just above this line:
`define( 'BUGNOTE_VIEW_INC_ALLOW', true );`

**Excluding top-level attachments from the Activity list:**
Add the following lines in `bugnote_view_inc.php`:
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

### Configuration
- **Customized Setup** (`default OFF`): Only change this setting to `ON` via the plugin configuration page if you want to customize the out-of-the-box behavior as described above.

---

## 🇪🇸 Documentación en Español

### Descripción	
Este plugin te permite reinstaurar la sección dedicada de adjuntos en los casos de tu Mantis Bug Tracker. Normalmente, MantisBT maneja los adjuntos como parte de las notas o dentro de la descripción principal. Este plugin proporciona un área optimizada específicamente para subir archivos adjuntos *sin* necesidad de escribir una nota de acompañamiento, haciendo que la gestión de documentos sea más rápida e intuitiva.

Ten en cuenta que: Los adjuntos subidos de esta manera no se muestran en la sección de actividades por defecto, pero aparecerán en su propio bloque visual dedicado justo debajo de la sección de actividades cuando visualices un caso.

Esta versión (3.1) agrega visualización de fecha de carga para cada adjunto y asegura compatibilidad total con MantisBT 2.28 y entornos modernos 2.X.

### Instalación
1. Sube la carpeta `Attachments` al directorio `plugins/` de tu instalación de MantisBT.
2. Ve a **Administración > Administrar Plugins** en tu interfaz de MantisBT.
3. Encuentra **Attachments** en la lista de Plugins Disponibles y haz clic en **Instalar**.

### Personalización
En caso de que quieras mostrar la sección de Adjuntos justo debajo de la sección principal del caso (como solía ser), debes hacer un cambio en un script principal (core).
**Ten en cuenta que estos cambios deben aplicarse de nuevo después de cada actualización de versión de Mantis.**

Para hacer esto, modifica `bug_view_inc.php`.
Agrega la siguiente línea:
`event_signal( 'EVENT_VIEW_BUG_FILES', array( $f_issue_id ) );`
justo antes del comentario: `# User list sponsoring the bug`

Si necesitas la sección de Adjuntos en la página de actualización `bug_update_page`, agrega la siguiente línea:
`event_signal( 'EVENT_VIEW_BUG_FILES', array( $t_bug_id ) );` 
Justo por encima de esta línea:
`define( 'BUGNOTE_VIEW_INC_ALLOW', true );`

**Excluir adjuntos de nivel superior de la lista de Actividades:**
Agrega las siguientes líneas en `bugnote_view_inc.php`:
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

### Configuración
- **Customized Setup** (`por defecto OFF`): Solamente cambia esta configuración a `ON` a través de la página de configuración del plugin si quieres aplicar las opciones de personalización descritas en la sección anterior.

---

### Change Log / Historial de Cambios
- **3.1:** Added upload date display for each attachment using EVENT_VIEW_BUG_ATTACHMENT hook (no core changes required).
- **3.0:** Verified 2.2X compatibility (tested on 2.28), bug fixes. Maintained by Cristobal Montenegro. 
- **2.25:** Added config option and improved readme.
- **2.20:** Made this section available while editing an issue.
- **2.10:** Added removed mantis function "function print_bug_attachments_list".
- **2.04:** Fixed impact on alignment on following blocks.
- **2.03:** Fixed not showing the top-level attachment in the activity section.
- **2.02:** Adjusted the layout of the Attachments block slightly. Included an option to exclude the top-level attachments from the activity list.