# TOTP - Autenticación de Doble Factor (2FA) / Two-Factor Authentication (2FA)

Plugin de MantisBT que agrega autenticación de doble factor (2FA) usando códigos TOTP (Time-based One-Time Password), compatible con aplicaciones como Google Authenticator, Authy, Microsoft Authenticator, etc.

MantisBT plugin that adds two-factor authentication (2FA) using TOTP codes (Time-based One-Time Password), compatible with apps like Google Authenticator, Authy, Microsoft Authenticator, etc.

---

## Características / Features

- **Autenticación opcional por usuario** / **Optional per-user authentication**: cada usuario decide si activa 2FA / each user decides whether to enable 2FA
- **Código QR**: configuración sencilla escaneando el código con la app / **QR Code**: easy setup by scanning with the auth app
- **Tolerancia a desfase horario**: ±30 segundos / **Time drift tolerance**: ±30 seconds
- **Validación de input**: solo acepta 6 dígitos numéricos / **Input validation**: 6-digit numeric only
- **Protección CSRF**: en todas las acciones / **CSRF protection**: on all actions
- **Sin modificaciones al core**: funciona 100% como plugin / **No core modifications**: 100% plugin-based using MantisBT hooks

## Requisitos / Requirements

- MantisBT 2.0.0 o superior / or higher
- PHP 7.4.0+

## Instalación / Installation

1. Copiar la carpeta `TOTP` en `mantis/plugins/` / Copy the `TOTP` folder to `mantis/plugins/`
2. Ir a **Admin > Manage > Manage Plugins** / Go to **Admin > Manage > Manage Plugins**
3. Hacer clic en **Install** junto a "Account TOTP MFA" / Click **Install** next to "Account TOTP MFA"
4. La tabla `mantis_plugin_TOTP_totp` se crea automáticamente / The `mantis_plugin_TOTP_totp` table is created automatically

## Uso / Usage

### Para usuarios / For users

1. Ir a **Mi Cuenta > Gestionar TOTP** / Go to **My Account > Manage TOTP**
2. Hacer clic en **Habilitar TOTP** / Click **Enable TOTP**
3. Escanear el código QR con la app de autenticación / Scan the QR code with your authentication app
4. A partir de ese momento, el login requerirá contraseña + código TOTP / From then on, login requires password + TOTP code

### Para administradores / For administrators

- **Configuración del issuer** / **Issuer configuration**: ir a **Admin > Manage > Manage Plugins > Account TOTP MFA** / go to **Admin > Manage > Manage Plugins > Account TOTP MFA**
- El issuer es el nombre que aparece en la app de autenticación / The issuer is the name shown in the authentication app (por defecto / default: "MantisBt Bug Tracker")

## Flujo de autenticación / Authentication flow

```
1. Usuario ingresa su nombre de usuario / User enters username
         ↓
2. Mantis verifica: ¿tiene TOTP configurado? / Mantis checks: TOTP configured?
         ↓
   ┌─── NO ──→ Login normal (usuario + contraseña) / Normal login (username + password)
   │
   └─── SÍ ──→ Redirige a página TOTP / Redirects to TOTP page
                (usuario + contraseña + código TOTP / username + password + TOTP code)
         ↓
3. Se verifica la contraseña / Password is verified
         ↓
4. Se verifica el código TOTP (con tolerancia ±30s) / TOTP code verified (±30s tolerance)
         ↓
5. Ambos correctos → Login exitoso / Both correct → Login success
   Alguno falla → Se incrementa contador de intentos / Either fails → Failed login count incremented
```

## Archivos / Files

| Archivo / File | Descripción / Description |
|----------------|---------------------------|
| `TOTP.php` | Clase principal del plugin / Main plugin class, hooks and configuration |
| `core/database.php` | Funciones de acceso a base de datos / Database access functions |
| `pages/login.php` | Handler de login con verificación TOTP / Login handler with TOTP verification |
| `pages/login-totp.php` | Página de login con campo TOTP / Login page with TOTP field |
| `pages/manage-totp.php` | Panel de gestión de TOTP / TOTP management panel |
| `pages/switch-totp-state.php` | Habilita/deshabilita TOTP / Enable/disable TOTP |
| `pages/render-qrcode.php` | Genera el código QR / QR code generator |
| `pages/config.php` | Configuración del issuer (admin) / Issuer configuration (admin) |
| `pages/config_update.php` | Guarda la configuración / Save configuration |
| `vendor/php-totp/` | Librería de tokens TOTP / TOTP token library |
| `vendor/phpqrcode/` | Librería de código QR / QR code library |
| `lang/strings_english.txt` | Textos en inglés / English strings |
| `lang/strings_french.txt` | Textos en francés / French strings |
| `lang/strings_spanish.txt` | Textos en español / Spanish strings |

## Base de datos / Database

El plugin crea una tabla: / The plugin creates a table:

```sql
mantis_plugin_TOTP_totp (
    user_id INT NOT NULL UNIQUE,
    secret_key VARCHAR(2000) NOT NULL UNIQUE
)
```

## Seguridad / Security

- Claves secretas generadas con `openssl_random_pseudo_bytes` (32 bytes) / Secret keys generated with `openssl_random_pseudo_bytes` (32 bytes)
- Tokens de 6 dígitos, intervalo de 30 segundos, algoritmo SHA1 / 6-digit tokens, 30-second interval, SHA1 algorithm
- Protección CSRF en todas las acciones / CSRF protection on all actions
- Queries parametrizadas para prevenir SQL injection / Parameterized queries to prevent SQL injection
- La clave secreta nunca se muestra en pantalla (solo el QR) / Secret key is never displayed (QR only)

## Créditos / Credits

- **Autor original / Original author**: BeYs Cloud (dev-cloud@be-ys.com) - https://www.be-ys.cloud
- **Adaptación y mejoras / Adaptation and improvements**: Cristobal Montenegro - https://github.com/cristobalmontenegro
- Licencia / License: GPLv3
