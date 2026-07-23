# MantisBT Resolution Access Control

**Version 1.0** *Compatible with MantisBT 2.X*

**Author:** Cristobal Montenegro ([@cristobalmontenegro](https://github.com/cristobalmontenegro))

---

## 🇬🇧 English Documentation

### Description

This plugin allows administrators to restrict who can modify the **Resolution** field of a bug. It provides both a server-side enforcement and a visual UI lock to ensure that users below a configured access level cannot change the resolution value.

When a user with insufficient privileges attempts to update a bug, the plugin silently reverts the resolution field to its previous value. Additionally, on the bug update and status change pages, the resolution dropdown is visually disabled (greyed out) for unauthorized users.

### How It Works

1. **Server-Side Protection:** The plugin hooks into `EVENT_UPDATE_BUG_DATA`. If the current user's access level is at or below the configured threshold, the resolution value from the existing bug data is restored, effectively ignoring any attempted change.

2. **Visual Lock:** The plugin injects CSS on `bug_update_page.php` and `bug_change_status_page.php` that disables the resolution `<select>` element (greyed out, non-clickable) for users who do not have sufficient access.

### Installation

1. Upload the `ResolutionAccess` folder to the `plugins/` directory of your MantisBT installation.
2. Go to **Manage > Manage Plugins** in your MantisBT interface.
3. Find **Resolution Access Control** in the list of Available Plugins and click **Install**.

### Configuration

Navigate to **Manage > Manage Plugins** and click on **Resolution Access Control** to configure the threshold.

-   **Readonly Threshold** (`default: UPDATER`): Users with this access level or below will be prevented from modifying the resolution field. Available levels:
    -   `VIEWER (10)`
    -   `REPORTER (25)`
    -   `UPDATER (40)`
    -   `DEVELOPER (55)`
    -   `MANAGER (70)`

For example, if set to `UPDATER`, any user with access level `UPDATER (40)` or lower (REPORTER, VIEWER) will not be able to change the resolution. `DEVELOPER` and above will retain full access.

### Requirements

-   **MantisBT**: Version 2.0.0 or higher.
-   **PHP**: Compatible with your MantisBT server.

---

## 🇪🇸 Documentación en Español

### Descripción

Este plugin permite a los administradores restringir quién puede modificar el campo **Resolución** de un caso. Proporciona tanto una validación del lado del servidor como un bloqueo visual en la interfaz, asegurando que los usuarios por debajo de un nivel de acceso configurado no puedan cambiar el valor de resolución.

Cuando un usuario con privilegios insuficientes intenta actualizar un caso, el plugin revierte silenciosamente el campo de resolución a su valor anterior. Además, en las páginas de actualización de casos y cambio de estado, el selector de resolución se deshabilita visualmente (aparece en gris y no es clickeable) para usuarios no autorizados.

### Cómo Funciona

1. **Protección del lado del servidor:** El plugin se engancha a `EVENT_UPDATE_BUG_DATA`. Si el nivel de acceso del usuario actual es igual o inferior al umbral configurado, el valor de resolución del caso existente se restaura, ignorando efectivamente cualquier cambio intentado.

2. **Bloqueo Visual:** El plugin inyecta CSS en `bug_update_page.php` y `bug_change_status_page.php` que deshabilita el elemento `<select>` de resolución (aparece en gris, sin posibilidad de clickeo) para usuarios que no tienen acceso suficiente.

### Instalación

1. Sube la carpeta `ResolutionAccess` al directorio `plugins/` de tu instalación de MantisBT.
2. Ve a **Administración > Administrar Plugins** en tu interfaz de MantisBT.
3. Encuentra **Resolution Access Control** en la lista de Plugins Disponibles y haz clic en **Instalar**.

### Configuración

Navega a **Administración > Administrar Plugins** y haz clic en **Resolution Access Control** para configurar el umbral.

-   **Nivel mínimo de solo lectura** (`por defecto: UPDATER`): Los usuarios con este nivel de acceso o inferior no podrán modificar el campo de resolución. Niveles disponibles:
    -   `VIEWER (10)`
    -   `REPORTER (25)`
    -   `UPDATER (40)`
    -   `DEVELOPER (55)`
    -   `MANAGER (70)`

Por ejemplo, si se configura en `UPDATER`, cualquier usuario con nivel de acceso `UPDATER (40)` o inferior (REPORTER, VIEWER) no podrá cambiar la resolución. `DEVELOPER` y superiores mantendrán acceso completo.

### Requisitos

-   **MantisBT**: Versión 2.0.0 o superior.
-   **PHP**: Compatible con tu servidor MantisBT.

---

### Change Log / Historial de Cambios

-   **1.0:** Initial release. Role-based access control for the Resolution field with server-side enforcement and visual UI lock.

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
