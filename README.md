# MantisBT Custom Reports

A powerful plugin for Mantis Bug Tracker that allows administrators to create, manage, and view custom SQL reports directly from the MantisBT interface. 

This plugin is especially useful for extracting specific metrics, consolidating case data, and generating tailored views for stakeholders without needing external database access.

## Features
- **Custom SQL Queries**: Execute tailored SQL queries directly against your MantisBT database.
- **Integrated Menu**: Adds a dedicated "Custom Reports" option to the MantisBT main menu.
- **Access Control**: Restrict report viewing and creation based on user access levels.
- **Export Options**: Export report results easily for external analysis.

## Requirements
- **MantisBT**: Version 2.0.0 or higher.
- **PHP**: Compatible with your MantisBT server.

## Installation
1. Download or clone this repository to your local machine.
2. Ensure the main plugin folder is named exactly **CustomReports**.
3. Move the `CustomReports` folder into the `plugins/` directory of your MantisBT installation.
4. Log into MantisBT with Administrator privileges.
5. Navigate to **Manage** -> **Manage Plugins**.
6. Locate "Mantis Custom Reports" in the list of Available Plugins.
7. Click the **Install** button.

## Configuration
After installation, you can configure the plugin by clicking on its name in the **Manage Plugins** section. From there, you can define the minimum access level (Threshold) required for users to view and manage these custom reports.

## Authors & Maintainers
- **Vincent Sels**: Original Author
- **Cristóbal Montenegro**: Current Maintainer (Mantis 2.x updates and improvements)

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

*🇪🇸 (Versión en Español)*

# Reportes Personalizados para MantisBT (Custom Reports)

Un potente plugin para Mantis Bug Tracker que permite a los administradores crear, administrar y visualizar reportes SQL personalizados directamente desde la interfaz de MantisBT.

Este plugin es especialmente útil para extraer métricas específicas, consolidar datos de casos y generar vistas adaptadas a las necesidades de la organización sin requerir acceso directo o externo a la base de datos.

## Características
- **Consultas SQL Personalizadas**: Permite la ejecución de queries a medida en la base de datos de Mantis.
- **Menú Integrado**: Agrega la opción "Custom Reports" al menú principal de MantisBT.
- **Control de Acceso**: Es posible restringir qué niveles de usuario pueden ver y administrar los reportes.

## Requisitos
- **MantisBT**: Versión 2.0.0 o superior.

## Instalación
1. Descarga o clona este repositorio.
2. Asegúrate de que la carpeta se llame **CustomReports**.
3. Copia la carpeta `CustomReports` al directorio `plugins/` de tu instalación de MantisBT.
4. Inicia sesión en MantisBT como Administrador.
5. Ve a **Administrar** -> **Administrar Plugins**.
6. Busca "Mantis Custom Reports" en la lista de plugins disponibles e instálalo.

## Licencia
Licencia MIT. Para más detalles revisa el archivo [LICENSE](LICENSE).

