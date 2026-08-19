# TOTP - Autenticación de Doble Factor (2FA)

Plugin de MantisBT que agrega autenticación de doble factor (2FA) usando códigos TOTP (Time-based One-Time Password), compatible con aplicaciones como Google Authenticator, Authy, Microsoft Authenticator, etc.

## Características

- **Autenticación opcional por usuario**: cada usuario decide si activa 2FA
- **Código QR**: configuración sencilla escaneando el código con la app de autenticación
- **Tolerancia a desfase horario**: ±30 segundos para evitar problemas de sincronización
- **Validación de input**: solo acepta códigos de 6 dígitos numéricos
- **Protección CSRF**: en todas las acciones de cambio de estado
- **Sin modificaciones al core**: funciona 100% como plugin usando hooks de MantisBT

## Requisitos

- MantisBT 2.0.0 o superior
- PHP 7.4.0+

## Instalación

1. Copiar la carpeta `TOTP` en `mantis/plugins/`
2. Ir a **Admin > Manage > Manage Plugins**
3. Hacer clic en **Install** junto a "Account TOTP MFA"
4. La tabla `mantis_plugin_TOTP_totp` se crea automáticamente

## Uso

### Para usuarios

1. Ir a **Mi Cuenta > Gestionar TOTP**
2. Hacer clic en **Habilitar TOTP**
3. Escanear el código QR con la app de autenticación
4. Ingresar el código de 6 dígitos generado por la app para confirmar
5. A partir de ese momento, el login requerirá contraseña + código TOTP

### Para administradores

- **Configuración del issuer**: ir a **Admin > Manage > Manage Plugins > Account TOTP MFA**
- El issuer es el nombre que aparece en la app de autenticación (por defecto: "MantisBt Bug Tracker")

## Flujo de autenticación

```
1. Usuario ingresa su nombre de usuario
         ↓
2. Mantis verifica: ¿tiene TOTP configurado?
         ↓
   ┌─── NO ──→ Login normal (usuario + contraseña)
   │
   └─── SÍ ──→ Redirige a página TOTP del plugin
                (usuario + contraseña + código TOTP)
         ↓
3. Se verifica la contraseña
         ↓
4. Se verifica el código TOTP (con tolerancia ±30s)
         ↓
5. Ambos correctos → Login exitoso
   Alguno falla → Se incrementa contador de intentos
```

## Archivos

| Archivo | Descripción |
|---------|-------------|
| `TOTP.php` | Clase principal del plugin, hooks y configuración |
| `core/database.php` | Funciones de acceso a base de datos |
| `pages/login.php` | Handler de login con verificación TOTP |
| `pages/login-totp.php` | Página de login con campo TOTP adicional |
| `pages/manage-totp.php` | Panel de gestión de TOTP para el usuario |
| `pages/switch-totp-state.php` | Habilita/deshabilita TOTP |
| `pages/render-qrcode.php` | Genera el código QR |
| `pages/config.php` | Configuración del issuer (admin) |
| `pages/config_update.php` | Guarda la configuración |
| `vendor/php-totp/` | Librería de generación de tokens TOTP |
| `vendor/phpqrcode/` | Librería de generación de código QR |
| `lang/strings_english.txt` | Textos en inglés |
| `lang/strings_french.txt` | Textos en francés |
| `lang/strings_spanish.txt` | Textos en español |

## Base de datos

El plugin crea una tabla:

```sql
mantis_plugin_TOTP_totp (
    user_id INT NOT NULL UNIQUE,
    secret_key VARCHAR(2000) NOT NULL UNIQUE
)
```

## Seguridad

- Claves secretas generadas con `openssl_random_pseudo_bytes` (32 bytes)
- Tokens de 6 dígitos, intervalo de 30 segundos, algoritmo SHA1
- Protección CSRF en todas las acciones
- Queries parametrizadas para prevenir SQL injection
- La clave secreta nunca se muestra en pantalla (solo el código QR)

## Créditos

- **Autor original**: BeYs Cloud (dev-cloud@be-ys.com) - https://www.be-ys.cloud
- **Adaptación y mejoras**: Cristobal Montenegro - https://github.com/cristobalmontenegro
- Licencia: GPLv3
