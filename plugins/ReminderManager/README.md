# MantisBT Reminder Manager

**Version 2.1.3** *Compatible with MantisBT 2.X*

**Author:** Cristobal Montenegro ([@cristobalmontenegro](https://github.com/cristobalmontenegro))

---

## 🇬🇧 English Documentation

### Description

**Reminder Manager** is an automation plugin for **MantisBT 2.X** designed to eliminate the manual overhead of following up on deadlines and scheduled activities within your bug tracker.

In professional issue tracking, many tasks are tied to specific dates (audit deadlines, promised delivery dates, follow-up meetings). Standard MantisBT requires manual checks to see which dates are approaching. **Reminder Manager** bridges this gap by allowing administrators to create dynamic rules that monitor **Custom Date Fields**. When a scheduled date is reached, the plugin automatically triggers personalized email notifications to the relevant stakeholders and performs a cleanup action to finalize the workflow.

### Key Features

-   **Dynamic Rule Engine:** Create multiple independent rules. Monitor one field for "Audits" and another for "Due Dates" simultaneously, each with its own logic.
-   **100% Customizable Templates:** Use a rich template editor for both **Email Subject** and **Email Body**.
-   **Smart Placeholders:** Inject real-time case data into your emails:
    -   `[[id]]`: Issue ID.
    -   `[[summary]]`: Issue title/summary.
    -   `[[status]]`: Current status (e.g., Assigned, Resolved).
    -   `[[handler]]`: Name of the person in charge.
    -   `[[reporter]]`: Name of the person who reported the issue.
    -   `[[link]]`: Direct clickable URL to the MantisBT issue.
    -   `[[custom_field_X]]`: Dynamically pull values from *other* custom fields (works in both subject and body).
-   **Granular Recipient Control:** Choose exactly who gets notified for each rule:
    -   The **Reporter** (to keep them informed).
    -   The **Handler** (to remind them of the task).
    -   **Monitors** (to keep supervisors in the loop).
-   **Automated Workflow Cleanup:** Once a notification is successfully sent, the plugin **clears the trigger date field**. This prevents notification loops and signals that the reminder has been processed.
-   **Audit Logging:** Every automated email is recorded in a dedicated log table within the MantisBT database. Logs can be queried directly via SQL for auditing purposes.
-   **Placeholder Insert Buttons:** The configuration UI includes clickable buttons to easily insert placeholders into your templates.
-   **Spanish Placeholder Auto-Correction:** If you accidentally use Spanish placeholders (`[[NOMBRE]]`, `[[RESUMEN]]`, `[[ESTADO]]`, `[[ASIGNADO]]`), they are automatically converted to their English equivalents.
-   **Enterprise Security:**
    -   Access restricted to users with direct administrative privileges.
    -   Cron execution protected via a unique **Security Token**.
    -   Integrates with MantisBT API for core operations.

### How It Works

1.  **Define a Rule:** Select a Custom Field (Type: Date) and set a preferred execution time (e.g., 09:00 AM).
2.  **Set the Trigger:** A user sets a date in that custom field within a MantisBT issue.
3.  **The Cron Job:** Your server's cron executes the processing script.
4.  **Notification:** If the current time is at or past the execution time AND the custom field date has been reached, the email is sent according to your template.
5.  **Completion:** The custom field value is cleared, and a log entry is created.

### Installation

1.  Download the repository and move the `ReminderManager` folder to your MantisBT `plugins/` directory.
2.  Navigate to **Manage > Manage Plugins**.
3.  Find **Reminder Manager** and click **Install**.
4.  Click on the plugin name to access the configuration dashboard.

### Automation (Cron Setup)

The processing script supports two execution modes:

**CLI (recommended):**
```bash
*/15 * * * * /usr/bin/php /path/to/mantis/plugins/ReminderManager/scripts/process.php
```

**HTTP/Web:**
```bash
*/15 * * * * curl -s "http://your-mantisbt-url/plugins/ReminderManager/scripts/process.php?token=YOUR_TOKEN"
```

The security token is displayed on the plugin configuration page.

### Requirements

-   **MantisBT**: Version 2.0.0 or higher.
-   **PHP**: Compatible with your MantisBT server.
-   **Cron**: Server-side cron job configuration (see above).

---

## 🇪🇸 Documentación en Español

### Descripción

**Reminder Manager** es un plugin de automatización para **MantisBT 2.X** diseñado para eliminar la carga manual de dar seguimiento a fechas de vencimiento y actividades programadas en tu bug tracker.

En la gestión profesional de incidencias, muchas tareas están ligadas a fechas específicas (vencimientos de auditoría, fechas de entrega, reuniones de seguimiento). El flujo estándar de MantisBT requiere revisiones manuales para saber qué fechas están próximas. **Reminder Manager** soluciona esto permitiendo a los administradores crear reglas dinámicas que monitorean **Campos Personalizados de Fecha**. Cuando se alcanza la fecha programada, el plugin dispara automáticamente notificaciones por correo personalizadas y realiza una acción de limpieza para finalizar el flujo.

### Características Principales

-   **Motor de Reglas Dinámico:** Crea múltiples reglas independientes. Monitorea un campo para "Auditorías" y otro para "Vencimientos" simultáneamente, cada uno con su propia lógica.
-   **Plantillas 100% Personalizables:** Editor completo para el **Asunto** y el **Cuerpo** del correo.
-   **Placeholders Inteligentes:** Inyecta datos del caso en tiempo real en tus correos:
    -   `[[id]]`: ID del caso.
    -   `[[summary]]`: Título/resumen del caso.
    -   `[[status]]`: Estado actual.
    -   `[[handler]]`: Nombre del responsable asignado.
    -   `[[reporter]]`: Nombre de quien reportó el caso.
    -   `[[link]]`: URL directa al caso en MantisBT.
    -   `[[custom_field_X]]`: Extrae valores de *otros* campos personalizados (funciona tanto en asunto como en cuerpo).
-   **Control Granular de Destinatarios:** Elige exactamente a quién notificar en cada regla:
    -   Al **Informador** (para mantenerlo al tanto).
    -   Al **Responsable** (como recordatorio de tarea).
    -   A los **Monitores** (para supervisión).
-   **Limpieza Automática de Flujo:** Una vez enviado el correo con éxito, el plugin **borra el valor del campo fecha**. Esto evita bucles de notificación y señala que el recordatorio ya fue procesado.
-   **Bitácora de Auditoría (Logs):** Cada envío queda registrado en una tabla de logs dentro de la base de datos de MantisBT. Los logs pueden consultarse directamente vía SQL para propósitos de auditoría.
-   **Botones de Inserción de Placeholders:** La interfaz de configuración incluye botones clickeables para insertar fácilmente placeholders en tus plantillas.
-   **Auto-corrección de Placeholders en Español:** Si accidentalmente usas placeholders en español (`[[NOMBRE]]`, `[[RESUMEN]]`, `[[ESTADO]]`, `[[ASIGNADO]]`), se convierten automáticamente a sus equivalentes en inglés.
-   **Seguridad Empresarial:**
    -   Acceso restringido a administradores.
    -   Ejecución de Cron protegida por un **Token de Seguridad** único.
    -   Integración con el API de MantisBT para operaciones principales.

### Cómo Funciona

1.  **Definir Regla:** Selecciona un Campo Personalizado (Tipo: Fecha) y una hora de ejecución (ej. 09:00 AM).
2.  **Activar:** Un usuario asigna una fecha en ese campo dentro de una incidencia de MantisBT.
3.  **El Cron:** El cron de tu servidor ejecuta el script de procesamiento.
4.  **Notificación:** Si la hora actual es igual o posterior a la hora de ejecución Y la fecha del campo fecha ha sido alcanzada, se envía el correo según tu plantilla.
5.  **Finalización:** Se borra el valor del campo personalizado y se genera un registro de log.

### Instalación

1.  Descarga el repositorio y mueve la carpeta `ReminderManager` al directorio `plugins/` de tu MantisBT.
2.  Ve a **Administrar > Administrar Complementos**.
3.  Busca **Reminder Manager** y haz clic en **Instalar**.
4.  Haz clic en el nombre del plugin para acceder al panel de configuración.

### Automatización (Configuración del Cron)

El script de procesamiento soporta dos modos de ejecución:

**CLI (recomendado):**
```bash
*/15 * * * * /usr/bin/php /path/to/mantis/plugins/ReminderManager/scripts/process.php
```

**HTTP/Web:**
```bash
*/15 * * * * curl -s "http://your-mantisbt-url/plugins/ReminderManager/scripts/process.php?token=YOUR_TOKEN"
```

El token de seguridad se muestra en la página de configuración del plugin.

### Requisitos

-   **MantisBT**: Versión 2.0.0 o superior.
-   **PHP**: Compatible con tu servidor MantisBT.
-   **Cron**: Configuración de cron del lado del servidor (ver arriba).

---

### Change Log / Historial de Cambios

-   **2.1.3:** Security hardening — execution time validation, custom field type validation, Spanish placeholder auto-correction.
-   **2.1.2:** Added placeholder insert buttons in UI.
-   **2.1.1:** Added HTTP/web execution mode with security token.
-   **2.1.0:** Added template subject field, recipient control (reporter/handler/monitors).
-   **2.0.0:** Major rewrite with rule management UI and audit logging.
-   **1.0.0:** Initial release with basic date monitoring.

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
