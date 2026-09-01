# MantisBT Suspend Toggle

**Version 1.1** *Compatible with MantisBT 2.X*

**Author:** cmc

---

## 🇬🇧 English Documentation

### Description

This plugin lets authorized users toggle a case's **Resolution** between **Open (10)** and **Suspended (80)** with a single button, **without changing the case status** and **without touching the MantisBT core**.

It was created to solve error **#24 (`ERROR_INVALID_RESOLUTION`)** that occurs when trying to set a resolution of "suspendido" (80) on a case whose status is below the resolved threshold (80). MantisBT's core validation in `bug_update.php` blocks any resolution `>= fixed (20)` when the status is `< resolved (80)`, which made it impossible to suspend a case like **0001731** (status 68 - "Mandamiento de Ejecución").

### How It Works

1. **Button:** The plugin injects a **Suspend / Resume** button in the case detail page (`EVENT_VIEW_BUG_DETAILS`). It is only shown to users meeting the configured access threshold.

2. **REST endpoint:** The button calls `POST /api/rest/index.php/plugins/SuspendToggle/toggle` (`EVENT_REST_API_ROUTES`). The endpoint authenticates via the web session cookie, re-checks the access threshold, and flips the resolution between `10` and `80`.

3. **Bypass of core validation:** The toggle saves using the model path `BugData::update()` (the same path used by the SOAP/REST APIs). **`BugData::update()` and its `validate()` method do NOT run the error #24 check** — that check only exists in the web flow `bug_update.php`. This is why the toggle works without modifying the core.

### Installation

1. Upload the `SuspendToggle` folder to the `plugins/` directory of your MantisBT installation.
2. Go to **Manage > Manage Plugins** in your MantisBT interface.
3. Find **Suspend Toggle** in the list of Available Plugins and click **Install**.

> The plugin registers its configuration defaults during installation. If you upgrade from `1.0`, the plugin's `upgrade()` method (schema 2) registers the new `block_status_gte` and `content_type_required` defaults automatically. If they don't appear after upgrading, uninstall and re-install the plugin, or run `plugin_upgrade()` by visiting the **Manage Plugins** page.

#### REST / Web-UI authentication notes

- The button (web UI) authenticates with your **web session cookie** (`credentials: 'same-origin'`), so it works even if the REST API is globally disabled.
- If you call the endpoint directly with a script, authenticate with the **`Authorization`** header: `Authorization: <YOUR_API_TOKEN>` (the `X-APIToken` header is NOT read by Mantis's `AuthMiddleware`). The token must be exactly **32 characters**.
- The endpoint requires `Content-Type: application/json` (enforced by default — see configuration) and rejects cross-origin requests.

### Configuration

Navigate to **Manage > Manage Plugins** and click on **Suspend Toggle** to configure the plugin.

- **Access level** (`default: MANAGER`): Users with this access level or above will see the button and be able to toggle the resolution. Available levels:
  - `VIEWER (10)`
  - `REPORTER (25)`
  - `UPDATER (40)`
  - `DEVELOPER (55)`
  - `MANAGER (70)`
- **Block toggle by status** (`default: 80` `"Resolved"`): The toggle is hidden/denied on issues whose **status** is equal to or greater than this value. Choose **"No limit (0)"** to not restrict by status. For example, with the default `80`, a case already `Resolved (80)` or `Closed (90)` cannot be toggled.
- **Require JSON Content-Type** (`default: ON`): When enabled, the REST endpoint rejects requests that do not send `Content-Type: application/json`. It is recommended to keep this enabled.

### Interaction with Resolution Access Control

No interference. The `ResolutionAccess` plugin only reverts resolution changes for access levels `<= UPDATER (40)`. Suspend Toggle requires `MANAGER+`, so a MANAGER's toggle is never reverted by that plugin.

### Security

In version `1.1` the plugin ships with three hardened checks on the REST endpoint:

1. **Cross-site request forgery (CSRF) mitigation** — `is_same_origin()` compares the request `Origin`/`Referer` header (scheme host + port) against the Mantis base path (`config_get_global('path')`). A mismatched origin returns `403`. Combined with `SameSite=Lax` session cookies, this blocks forged cross-site toggles.
2. **Strict Content-Type** — when `content_type_required` is `ON` (default), requests that do not send `Content-Type: application/json` are rejected with `400`.
3. **Status restriction** — `is_toggle_allowed()` combines the access-level check with `block_status_gte`, so the button is not rendered and the endpoint refuses to toggle issues whose status is at or above the configured value.

The button and the `<script>` are only injected when the current user is allowed to toggle, and the endpoint re-checks authorization server-side (never trusting the UI alone).

### Files

- `SuspendToggle.php` — main plugin class
- `pages/config.php` / `pages/config_edit.php` — configuration UI
- `files/suspendtoggle.js` — front-end logic (button -> REST call)
- `lang/strings_english.txt` / `lang/strings_spanish.txt`

### Requirements

- **MantisBT**: Version 2.0.0 or higher (tested on 2.28.x).
- **PHP**: Compatible with your MantisBT server.

---

## 🇪🇸 Documentación en Español

### Descripción

Este plugin permite a los usuarios autorizados alternar la **Resolución** de un caso entre **Abierto (10)** y **Suspendido (80)** con un solo botón, **sin cambiar el estado del caso** y **sin tocar el core de MantisBT**.

Se creó para resolver el error **#24 (`ERROR_INVALID_RESOLUTION`)**, que aparece al intentar fijar una resolución "suspendido" (80) en un caso cuyo estado está por debajo del umbral de resuelto (80). La validación del core en `bug_update.php` bloquea cualquier resolución `>= fija (20)` cuando el estado es `< resuelto (80)`, lo que hacía imposible suspender un caso como el **0001731** (estado 68 - "Mandamiento de Ejecución").

### Cómo Funciona

1. **Botón:** El plugin inyecta un botón **Suspender / Reanudar** en la página de detalle del caso (`EVENT_VIEW_BUG_DETAILS`). Solo se muestra a usuarios que cumplen el umbral de acceso configurado.

2. **Endpoint REST:** El botón llama a `POST /api/rest/index.php/plugins/SuspendToggle/toggle` (`EVENT_REST_API_ROUTES`). El endpoint se autentica por la cookie de sesión web, vuelve a verificar el umbral de acceso, y alterna la resolución entre `10` y `80`.

3. **Elusión de la validación del core:** El botón guarda por la ruta del modelo `BugData::update()` (la misma que usan las APIs SOAP/REST). **`BugData::update()` y su método `validate()` NO ejecutan la comprobación del error #24** — esa comprobación solo existe en el flujo web `bug_update.php`. Por eso el botón funciona sin modificar el core.

### Instalación

1. Sube la carpeta `SuspendToggle` al directorio `plugins/` de tu instalación de MantisBT.
2. Ve a **Administración > Administrar Plugins** en tu interfaz de MantisBT.
3. Encuentra **Suspend Toggle** en la lista de Plugins Disponibles y haz clic en **Instalar**.

> El plugin registra sus valores de configuración por defecto durante la instalación. Si actualizas desde `1.0`, el método `upgrade()` (schema 2) registra automáticamente los nuevos valores `block_status_gte` y `content_type_required`. Si no aparecen tras actualizar, desinstala y reinstala el plugin, o ejecuta `plugin_upgrade()` visitando la página **Administrar Plugins**.

#### Notas de autenticación REST / interfaz web

- El botón (interfaz web) se autentica con la **cookie de tu sesión web** (`credentials: 'same-origin'`), por lo que funciona incluso si la API REST está deshabilitada globalmente.
- Si llamas al endpoint directamente con un script, autentícate con el header **`Authorization`**: `Authorization: <TU_API_TOKEN>` (el header `X-APIToken` NO lo lee el `AuthMiddleware` de Mantis). El token debe tener exactamente **32 caracteres**.
- El endpoint exige `Content-Type: application/json` (activado por defecto — ver configuración) y rechaza peticiones cross-origin.

### Configuración

Navega a **Administración > Administrar Plugins** y haz clic en **Suspend Toggle** para configurar el plugin.

- **Nivel de acceso** (`por defecto: MANAGER`): Los usuarios con este nivel de acceso o superior verán el botón y podrán alternar la resolución. Niveles disponibles:
  - `VIEWER (10)`
  - `REPORTER (25)`
  - `UPDATER (40)`
  - `DEVELOPER (55)`
  - `MANAGER (70)`
- **Bloquear toggle por estado** (`por defecto: 80` `"Resuelto"`): El toggle se oculta/rechaza en casos cuyo **estado** es igual o superior a este valor. Elige **"Sin límite (0)"** para no restringir por estado. Por ejemplo, con el valor por defecto `80`, un caso ya `Resuelto (80)` o `Cerrado (90)` no puede alternarse.
- **Exigir Content-Type JSON** (`por defecto: ON`): Cuando está activado, el endpoint REST rechaza peticiones que no envíen `Content-Type: application/json`. Se recomienda mantenerlo activado.

### Interacción con Resolution Access Control

Sin interferencias. El plugin `ResolutionAccess` solo revierte cambios de resolución para niveles de acceso `<= UPDATER (40)`. Suspend Toggle exige `MANAGER+`, por lo que la alternancia de un MANAGER nunca es revertida por ese plugin.

### Seguridad

En la versión `1.1` el plugin incorpora tres comprobaciones reforzadas en el endpoint REST:

1. **Mitigación de CSRF** — `is_same_origin()` compara el header `Origin`/`Referer` de la petición (esquema, host y puerto) contra la URL base de Mantis (`config_get_global('path')`). Un origen distinto devuelve `403`. Combinado con cookies de sesión `SameSite=Lax`, bloquea toggles forjados desde otros sitios.
2. **Content-Type estricto** — cuando `content_type_required` está `ON` (por defecto), las peticiones que no envíen `Content-Type: application/json` se rechazan con `400`.
3. **Restricción por estado** — `is_toggle_allowed()` combina la comprobación de nivel de acceso con `block_status_gte`, de modo que el botón no se muestra y el endpoint rechaza alternar casos cuyo estado esté en o por encima del valor configurado.

El botón y el `<script>` solo se inyectan cuando el usuario actual tiene permiso para alternar, y el endpoint vuelve a comprobar la autorización en el servidor (nunca confía solo en la interfaz).

### Archivos

- `SuspendToggle.php` — clase principal del plugin
- `pages/config.php` / `pages/config_edit.php` — interfaz de configuración
- `files/suspendtoggle.js` — lógica del front (botón -> llamada REST)
- `lang/strings_spanish.txt` / `lang/strings_english.txt`

### Requisitos

- **MantisBT**: Versión 2.0.0 o superior (probado en 2.28.x).
- **PHP**: Compatible con tu servidor MantisBT.

---

- **1.1:** Refuerzos de seguridad en el endpoint REST: comprobación de origen (`Origin`/`Referer`) contra la URL base, exigencia de `Content-Type: application/json` configurable, y restricción del toggle por estado (`block_status_gte`, `default 80`). Boletín de errores corregido (botón/script ocultos en estados bloqueados). Registro de nuevos valores de configuración vía `install()`/`upgrade()` (schema 2).
- **1.0:** Publicación inicial. Botón de suspender/reanudar resolución con validación server-side por nivel de acceso y elusión del error #24 mediante la ruta del modelo `BugData::update()`.

---

*Desarrollado por cmc*
