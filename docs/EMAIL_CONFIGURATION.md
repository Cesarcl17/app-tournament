# Configuración de Email - App Tournament

## Desarrollo (por defecto)

En desarrollo, los emails se guardan en los logs (`storage/logs/laravel.log`).

```env
MAIL_MAILER=log
```

## Producción con Gmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="tu_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> **Importante**: Usa una "App Password" de Google, no tu contraseña normal.
> Ve a: Cuenta Google → Seguridad → Verificación en 2 pasos → Contraseñas de aplicaciones

## Producción con Mailtrap (Testing)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username
MAIL_PASSWORD=tu_password
MAIL_FROM_ADDRESS="noreply@tournament.app"
MAIL_FROM_NAME="${APP_NAME}"
```

## Producción con SendGrid

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=tu_sendgrid_api_key
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Producción con Mailgun

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=tu_dominio
MAILGUN_SECRET=tu_api_key
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Verificar configuración

Ejecuta el siguiente comando para probar el envío de email:

```bash
php artisan tinker
>>> Notification::route('mail', 'tu_email@test.com')->notify(new App\Notifications\MatchScheduled($match));
```

O simplemente revisa los logs:

```bash
tail -f storage/logs/laravel.log
```

## Notificaciones que envía la aplicación

- **TeamInvitationReceived**: Cuando invitas a alguien a tu equipo
- **TeamRequestApproved**: Cuando aprueban tu solicitud de unirse
- **TeamRequestRejected**: Cuando rechazan tu solicitud
- **MatchScheduled**: Cuando se programa una partida
- **MatchStartingSoon**: Recordatorio antes de una partida
- **MatchResultReported**: Cuando se reporta un resultado
- **MatchDisputed**: Cuando hay disputa en un resultado
- **DisputeResolved**: Cuando se resuelve una disputa
- **BracketGenerated**: Cuando se genera el bracket
- **TournamentStartingSoon**: Recordatorio de inicio de torneo
