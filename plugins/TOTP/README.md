# MantisBT - TOTP Plugin

**Version 1.1.0** *Compatible with MantisBT 2.X*

**Original Author:** BeYs Cloud ([@be-ys-cloud](https://github.com/be-ys-cloud)) - [MantisBT-TOTP-Plugin](https://github.com/be-ys-cloud/MantisBT-TOTP-Plugin)
**Maintained by:** Cristobal Montenegro ([@cristobalmontenegro](https://github.com/cristobalmontenegro))

---

## 🇬🇧 English Documentation

### Description

A plugin for MantisBT that adds two-factor authentication (2FA) using TOTP codes (Time-based One-Time Password), compatible with apps like Google Authenticator, Authy, Microsoft Authenticator, etc.

### Features

- **Optional per-user authentication**: each user decides whether to enable 2FA
- **QR Code**: easy setup by scanning with the authentication app
- **Time drift tolerance**: ±30 seconds to avoid sync issues
- **Input validation**: 6-digit numeric codes only
- **CSRF protection**: on all state-changing actions
- **No core modifications**: 100% plugin-based using MantisBT hooks

### Requirements

- MantisBT 2.0.0 or higher
- PHP 7.4.0+

### Installation

1. Copy the `TOTP` folder to `mantis/plugins/`
2. Go to **Admin > Manage > Manage Plugins**
3. Click **Install** next to "Account TOTP MFA"
4. The `mantis_plugin_TOTP_totp` table is created automatically

### Usage

#### For users

1. Go to **My Account > Manage TOTP**
2. Click **Enable TOTP**
3. Scan the QR code with your authentication app
4. From then on, login requires password + TOTP code

#### For administrators

- **Issuer configuration**: go to **Admin > Manage > Manage Plugins > Account TOTP MFA**
- The issuer is the name shown in the authentication app (default: "MantisBt Bug Tracker")

### Authentication Flow

```
1. User enters username
         ↓
2. Mantis checks: TOTP configured?
         ↓
   ┌─── NO  ──→ Normal login (username + password)
   │
   └─── YES ──→ Redirects to plugin's TOTP page
                (username + password + TOTP code)
         ↓
3. Password is verified
         ↓
4. TOTP code verified (±30s tolerance)
         ↓
5. Both correct → Login success
   Either fails → Failed login count incremented
```

### Files

| File | Description |
|------|-------------|
| `TOTP.php` | Main plugin class, hooks and configuration |
| `core/database.php` | Database access functions |
| `pages/login.php` | Login handler with TOTP verification |
| `pages/login-totp.php` | Login page with TOTP field |
| `pages/manage-totp.php` | TOTP management panel |
| `pages/switch-totp-state.php` | Enable/disable TOTP |
| `pages/render-qrcode.php` | QR code generator |
| `pages/config.php` | Issuer configuration (admin) |
| `pages/config_update.php` | Save configuration |
| `vendor/php-totp/` | TOTP token library |
| `vendor/phpqrcode/` | QR code library |
| `lang/strings_english.txt` | English strings |
| `lang/strings_french.txt` | French strings |
| `lang/strings_spanish.txt` | Spanish strings |

### Database

The plugin creates a table:

```sql
mantis_plugin_TOTP_totp (
    user_id INT NOT NULL UNIQUE,
    secret_key VARCHAR(2000) NOT NULL UNIQUE
)
```

### Security

- Secret keys generated with `openssl_random_pseudo_bytes` (32 bytes)
- 6-digit tokens, 30-second interval, SHA1 algorithm
- CSRF protection on all actions
- Parameterized queries to prevent SQL injection
- Secret key is never displayed (QR code only)

### Change Log

- **1.1.0:** Security hardening — SQL injection fixes, CSRF protection, time drift tolerance, input validation, Spanish language support
- **1.0.0:** Initial release by BeYs Cloud

---

## 🇪🇸 Documentación en Español

### Descripción

Plugin de MantisBT que agrega autenticación de doble factor (2FA) usando códigos TOTP (Time-based One-Time Password), compatible con aplicaciones como Google Authenticator, Authy, Microsoft Authenticator, etc.

### Características

- **Autenticación opcional por usuario**: cada usuario decide si activa 2FA
- **Código QR**: configuración sencilla escaneando el código con la app de autenticación
- **Tolerancia a desfase horario**: ±30 segundos para evitar problemas de sincronización
- **Validación de input**: solo acepta 6 dígitos numéricos
- **Protección CSRF**: en todas las acciones de cambio de estado
- **Sin modificaciones al core**: funciona 100% como plugin usando hooks de MantisBT

### Requisitos

- MantisBT 2.0.0 o superior
- PHP 7.4.0+

### Instalación

1. Copiar la carpeta `TOTP` en `mantis/plugins/`
2. Ir a **Admin > Manage > Manage Plugins**
3. Hacer clic en **Install** junto a "Account TOTP MFA"
4. La tabla `mantis_plugin_TOTP_totp` se crea automáticamente

### Uso

#### Para usuarios

1. Ir a **Mi Cuenta > Gestionar TOTP**
2. Hacer clic en **Habilitar TOTP**
3. Escanear el código QR con la app de autenticación
4. A partir de ese momento, el login requerirá contraseña + código TOTP

#### Para administradores

- **Configuración del issuer**: ir a **Admin > Manage > Manage Plugins > Account TOTP MFA**
- El issuer es el nombre que aparece en la app de autenticación (por defecto: "MantisBt Bug Tracker")

### Flujo de autenticación

```
1. Usuario ingresa su nombre de usuario
         ↓
2. Mantis verifica: ¿tiene TOTP configurado?
         ↓
   ┌─── NO  ──→ Login normal (usuario + contraseña)
   │
   └─── SÍ  ──→ Redirige a página TOTP del plugin
                (usuario + contraseña + código TOTP)
         ↓
3. Se verifica la contraseña
         ↓
4. Se verifica el código TOTP (con tolerancia ±30s)
         ↓
5. Ambos correctos → Login exitoso
   Alguno falla → Se incrementa contador de intentos
```

### Archivos

| Archivo | Descripción |
|---------|-------------|
| `TOTP.php` | Clase principal del plugin, hooks y configuración |
| `core/database.php` | Funciones de acceso a base de datos |
| `pages/login.php` | Handler de login con verificación TOTP |
| `pages/login-totp.php` | Página de login con campo TOTP |
| `pages/manage-totp.php` | Panel de gestión de TOTP |
| `pages/switch-totp-state.php` | Habilita/deshabilita TOTP |
| `pages/render-qrcode.php` | Genera el código QR |
| `pages/config.php` | Configuración del issuer (admin) |
| `pages/config_update.php` | Guarda la configuración |
| `vendor/php-totp/` | Librería de tokens TOTP |
| `vendor/phpqrcode/` | Librería de código QR |
| `lang/strings_english.txt` | Textos en inglés |
| `lang/strings_french.txt` | Textos en francés |
| `lang/strings_spanish.txt` | Textos en español |

### Base de datos

El plugin crea una tabla:

```sql
mantis_plugin_TOTP_totp (
    user_id INT NOT NULL UNIQUE,
    secret_key VARCHAR(2000) NOT NULL UNIQUE
)
```

### Seguridad

- Claves secretas generadas con `openssl_random_pseudo_bytes` (32 bytes)
- Tokens de 6 dígitos, intervalo de 30 segundos, algoritmo SHA1
- Protección CSRF en todas las acciones
- Queries parametrizadas para prevenir SQL injection
- La clave secreta nunca se muestra en pantalla (solo el código QR)

### Historial de Cambios

- **1.1.0:** Endurecimiento de seguridad — correcciones de SQL injection, protección CSRF, tolerancia a desfase horario, validación de input, soporte de idioma español
- **1.0.0:** Versión inicial por BeYs Cloud

---

*Developed by [Cristobal Montenegro](https://github.com/cristobalmontenegro)*
