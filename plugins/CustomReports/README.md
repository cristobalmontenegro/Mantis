# MantisBT Custom Reports

**Version 2.1.0** *Compatible with MantisBT 2.X*

**Author:** Cristobal Montenegro ([@cristobalmontenegro](https://github.com/cristobalmontenegro)) / Based on the work of Vincent Sels

---

## 🇬🇧 English Documentation

### Description

A powerful plugin for Mantis Bug Tracker that allows administrators to create, manage, and view custom SQL reports directly from the MantisBT interface. This plugin is especially useful for extracting specific metrics, consolidating case data, and generating tailored views for stakeholders without needing external database access.

### Features

- **Custom SQL Queries**: Execute tailored SQL queries directly against your MantisBT database.
- **Integrated Menu**: Adds a dedicated "Custom Reports" option to the MantisBT main menu.
- **Dual Access Control**: Two separate threshold levels for fine-grained permissions:
    - **Admin Threshold**: Controls who can create, edit, and delete reports and access plugin configuration.
    - **View Threshold**: Controls who can see the menu item and run/view reports.
- **Date Range Parameters**: Use `:period_start` and `:period_end` placeholders in your SQL queries to let users filter by date ranges.
- **Smart Filter**: Client-side filtering of report results with support for exclusion terms (prefix with `-`).
- **Column Sorting**: Click column headers to sort report results ascending or descending.
- **CSV Export**: Export report results to CSV (semicolon-delimited, UTF-8 with BOM for Excel compatibility).
- **SQL Security**: Automatically blocks queries containing DDL/DCL keywords (INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, TRUNCATE) — only SELECT queries are allowed.
- **CSRF Protection**: All forms include CSRF token validation.

### Requirements

- **MantisBT**: Version 2.0.0 or higher.

### Installation

1. Download or clone this repository to your local machine.
2. Ensure the main plugin folder is named exactly **CustomReports**.
3. Move the `CustomReports` folder into the `plugins/` directory of your MantisBT installation.
4. Log into MantisBT with Administrator privileges.
5. Navigate to **Manage** -> **Manage Plugins**.
6. Locate "Custom Reports" in the list of Available Plugins.
7. Click the **Install** button.

### Configuration

After installation, click on the plugin name in **Manage Plugins** to access the configuration dashboard. The plugin provides a tabbed interface:

-   **Plugin Configuration**: Set the two access thresholds:
    -   **Admin Threshold**: Minimum access level to manage reports (create/edit/delete) and configure the plugin.
    -   **View Threshold**: Minimum access level to see the "Custom Reports" menu and run/view reports.
-   **Manage Reports**: Create, edit, and delete SQL reports. Each report has:
    -   **Name**: A descriptive name for the report.
    -   **View Threshold**: Per-report access level for viewing.
    -   **SQL Query**: The SELECT query to execute. Use `:period_start` and `:period_end` as date placeholders.
    -   **Include Period**: Enable date range parameter inputs for this report.

---

## 🇪🇸 Documentación en Español

### Descripción

Un potente plugin para Mantis Bug Tracker que permite a los administradores crear, administrar y visualizar reportes SQL personalizados directamente desde la interfaz de MantisBT. Este plugin es especialmente útil para extraer métricas específicas, consolidar datos de casos y generar vistas adaptadas a las necesidades de la organización sin requerir acceso directo a la base de datos.

### Características

- **Consultas SQL Personalizadas**: Permite la ejecución de queries a medida en la base de datos de Mantis.
- **Menú Integrado**: Agrega la opción "Custom Reports" al menú principal de MantisBT.
- **Control de Acceso Dual**: Dos niveles de umbral separados para permisos granulares:
    -   **Umbral de Administración**: Controla quién puede crear, editar y eliminar reportes y acceder a la configuración.
    -   **Umbral de Lectura**: Controla quién puede ver el menú y ejecutar/visualizar reportes.
- **Parámetros de Rango de Fechas**: Usa los marcadores `:period_start` y `:period_end` en tus queries SQL para permitir a los usuarios filtrar por rangos de fecha.
- **Filtro Inteligente**: Filtrado del lado del cliente de los resultados con soporte para términos de exclusión (prefijo `-`).
- **Ordenamiento por Columnas**: Haz clic en los encabezados de columna para ordenar resultados ascendente o descendente.
- **Exportación CSV**: Exporta resultados a CSV (delimitado por punto y coma, UTF-8 con BOM para compatibilidad con Excel).
- **Seguridad SQL**: Bloquea automáticamente queries que contengan palabras clave DDL/DCL (INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, TRUNCATE) — solo se permiten queries SELECT.
- **Protección CSRF**: Todos los formularios incluyen validación de token CSRF.

### Requisitos

-   **MantisBT**: Versión 2.0.0 o superior.

### Instalación

1.  Descarga o clona este repositorio.
2.  Asegúrate de que la carpeta se llame **CustomReports**.
3.  Copia la carpeta `CustomReports` al directorio `plugins/` de tu instalación de MantisBT.
4.  Inicia sesión en MantisBT como Administrador.
5.  Ve a **Administrar** -> **Administrar Plugins**.
6.  Busca "Custom Reports" en la lista de plugins disponibles e instálalo.

### Configuración

Después de la instalación, haz clic en el nombre del plugin en **Administrar Plugins** para acceder al panel de configuración. El plugin proporciona una interfaz con pestañas:

-   **Configuración del Plugin**: Establece los dos umbrales de acceso:
    -   **Umbral de Administración**: Nivel mínimo de acceso para gestionar reportes (crear/editar/eliminar) y configurar el plugin.
    -   **Umbral de Lectura**: Nivel mínimo de acceso para ver el menú "Custom Reports" y ejecutar/visualizar reportes.
-   **Administrar Reportes**: Crea, edita y elimina reportes SQL. Cada reporte tiene:
    -   **Nombre**: Un nombre descriptivo para el reporte.
    -   **Umbral de Lectura**: Nivel de acceso por reporte para visualización.
    -   **Consulta SQL**: La query SELECT a ejecutar. Usa `:period_start` y `:period_end` como marcadores de fecha.
    -   **Incluir Período**: Habilita entradas de rango de fecha para este reporte.

---

### Change Log / Historial de Cambios

-   **2.1.0:** Security hardening — CSRF validation, SQL keyword blocking, date sanitization. Fixed HTML typo, replaced hardcoded Spanish strings. Added smart filter and column sorting.
-   **2.0.5:** Added smart filter UI, column sorting, AJAX-based period toggle.
-   **2.0.0:** Major rewrite with dual threshold system, report management CRUD, CSV export.
-   **1.0.5:** Added period parameters and date picker support.
-   **1.0.0:** Initial release with basic SQL report execution.

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
