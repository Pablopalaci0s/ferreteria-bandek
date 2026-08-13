# Backups de la base de datos (BANDEK)

Sistema de backups de `bandek_db`: copia diaria comprimida, con rotación,
copia off-site opcional a Google Drive, y restauración.

## Comandos

```bash
# Crear un backup ahora (local + copia off-site si está configurada)
php artisan db:backup

# Listar los backups disponibles
php artisan db:restore

# Restaurar un backup (⚠️ SOBRESCRIBE los datos actuales)
php artisan db:restore bandek_db-AAAA-MM-DD_HHMMSS.sql.gz
```

Los backups locales se guardan en `storage/app/backups/` (fuera de git).

## Automático (diario)

Ya está programado a las **03:00** en `routes/console.php`. Para que el
programador de Laravel corra, el sistema operativo debe invocar cada minuto:

```bash
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

- **macOS (dev):** cron solo corre con el equipo despierto. Si a las 03:00
  está dormido, ese backup se saltea. Cambiá el horario a uno de uso, o
  confiá el backup diario al servidor de producción.
- **Producción (Linux):** el mismo cron es 100% confiable.

## Configuración (.env)

```env
DB_DUMP_BINARY_PATH=/usr/local/mysql/bin   # carpeta de mysqldump/mysql (vacío = PATH)
DB_BACKUP_RETENTION_DAYS=7                  # días de copias que se conservan

# Copia off-site (opcional pero MUY recomendada)
DB_BACKUP_UPLOAD_DISK=google               # vacío = solo local
DB_BACKUP_UPLOAD_FOLDER=bandek-backups
```

> ⚠️ **Sin copia off-site, el backup y la BD están en el mismo disco.**
> Si el disco muere, se pierden los dos. Configurá Google Drive (abajo).

## Copia off-site a Google Drive — configuración por única vez

1. **Google Cloud Console** (https://console.cloud.google.com):
   - Creá un proyecto.
   - Habilitá la **Google Drive API**.
   - Creá credenciales **OAuth client ID** de tipo *Web application*.
   - En "Authorized redirect URIs" agregá:
     `https://developers.google.com/oauthplayground`
   - Guardá el **Client ID** y el **Client Secret**.

2. **Obtener el refresh token** (https://developers.google.com/oauthplayground):
   - Botón de engranaje (arriba a la derecha) → *Use your own OAuth credentials*
     y pegá tu Client ID y Secret.
   - En la lista, elegí el scope: `https://www.googleapis.com/auth/drive`
   - *Authorize APIs* → iniciá sesión con la cuenta de Drive donde querés los backups.
   - *Exchange authorization code for tokens* → copiá el **Refresh token**.

3. **(Opcional) Carpeta de destino:** creá una carpeta en Drive, abrila, y
   copiá el ID que aparece en la URL (`.../folders/ESTE_ID`).

4. **Completá el `.env`:**
   ```env
   DB_BACKUP_UPLOAD_DISK=google
   GOOGLE_DRIVE_CLIENT_ID=xxxx.apps.googleusercontent.com
   GOOGLE_DRIVE_CLIENT_SECRET=xxxx
   GOOGLE_DRIVE_REFRESH_TOKEN=xxxx
   GOOGLE_DRIVE_FOLDER_ID=xxxx   # opcional
   ```

5. **Probá:**
   ```bash
   php artisan config:clear
   php artisan db:backup
   ```
   Debería decir `Copia subida a «google»: bandek-backups/...`.

La rotación por días también se aplica en Drive: las copias viejas se borran solas.
