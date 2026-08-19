# TODO - Plugin TOTP

## Pendientes

- [ ] ~~Mostrar columna TOTP en lista de usuarios~~ — No hay evento en MantisBT para esto, requiere modificar el core
- [ ] Agregar campo `enabled` a la tabla TOTP (para deshabilitar sin borrar la clave secreta)
- [ ] Soporte idiomas adicionales (portugués, etc.)
- [ ] Paginación en admin disable (si hay muchos usuarios)

## Por verificar

- [ ] Login completo con TOTP (credenciales + código) — probar flujo completo
- [ ] Bloqueo de sesión tras múltiples intentos fallidos de TOTP
- [ ] Compatible con Google Authenticator, Authy, Microsoft Authenticator
- [ ] Backup codes (códigos de recuperación)

## Bugs conocidos

- Ninguno reportado por el usuario actualmente

---

*Última actualización: 2026-08-18*
