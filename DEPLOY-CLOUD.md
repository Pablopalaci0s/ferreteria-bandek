# Desplegar BANDEK 100% gratis (Render + Neon + Cloudflare R2)

Guía para poner el sitio en línea sin pagar nada, usando capas gratuitas:

| Pieza | Servicio | Por qué este y no otro |
|---|---|---|
| Servidor (PHP) | **Render** (Docker, plan Free) | Soporta cualquier lenguaje vía Docker; 750h/mes gratis (alcanza para 1 servicio corriendo todo el mes) |
| Base de datos | **Neon** (Postgres) | Free forever, sin tarjeta. Se "duerme" sola tras 5 min sin uso y despierta sola en la siguiente consulta (~300ms) — no hace falta entrar a reactivarla a mano |
| Imágenes | **Cloudflare R2** (S3-compatible) | Render free NO tiene disco persistente: las imágenes subidas se perderían en cada reinicio si se guardan en el propio contenedor. R2 da 10GB gratis y sin costo de salida (egress) |
| Correo | **Resend** (ya en el proyecto) | Ya configurado; solo falta verificar el dominio |
| Backups off-site | **Google Drive** (ya en el proyecto) | Gratis, privado, ya implementado — no hace falta otro servicio más |
| Cron diario | **GitHub Actions** (gratis) | Render free no ofrece cron gratis (mínimo $1/mes); GitHub Actions sí |

⚠️ **Por qué no Supabase para la base de datos**: su plan free *pausa el
proyecto* tras una semana sin actividad, y reactivarlo requiere entrar al
dashboard a mano — con el tráfico bajo que va a tener el sitio al
arrancar, esto pasaría seguido y el sitio quedaría caído hasta que alguien
lo note. Neon resuelve esto solo, sin intervención. (Podés usar Supabase
solo si en algún momento preferís su Storage en vez de R2 — la config de
abajo es igual de válida para cualquier proveedor S3-compatible.)

## Qué cambió en el código para que esto funcione

Ya está hecho y commiteado, pero para que entiendas qué se tocó:

- **Base de datos**: Laravel ya soporta Postgres de fábrica
  ([config/database.php](config/database.php)) — se revisaron todas las
  queries del proyecto (migraciones, `DB::raw`, `whereRaw`) y no hay nada
  específico de MySQL que no funcione en Postgres.
- **Imágenes**: [ImagenOptimizada](app/Support/ImagenOptimizada.php) ya no
  asume un disco local — ahora renderiza a un archivo temporal y lo sube al
  disco `public` con `Storage::put()`, que funciona igual con disco local o
  con un bucket S3-compatible. Todas las vistas que armaban la URL de una
  imagen a mano (`asset('storage/'.$ruta)`) ahora usan
  `Storage::disk('public')->url($ruta)`, que resuelve bien en cualquiera de
  los dos casos.
- **Backups**: [BackupBaseDatos](app/Console/Commands/BackupBaseDatos.php)
  y [RestaurarBaseDatos](app/Console/Commands/RestaurarBaseDatos.php)
  ahora soportan tanto `mysqldump`/`mysql` como `pg_dump`/`psql`.
- **Cron sin cron real**: nueva ruta `GET /cron/backup`
  ([CronController](app/Http/Controllers/CronController.php)), protegida
  por la cabecera `X-Cron-Secret`, que corre `db:backup` bajo demanda.

Nada de esto afecta tu entorno local: si no configurás las variables
nuevas, todo sigue usando disco local y MySQL como hasta ahora.

---

## 1. Neon (base de datos)

1. Creá cuenta en [neon.com](https://neon.com) (sin tarjeta).
2. **New Project** → nombre `bandek`, región la más cercana a tus
   clientes (ej. AWS us-east-1).
3. En el dashboard del proyecto, pestaña **Connect** → elegí la connection
   string con **pooled connection** (usa PgBouncer, mejor para una app web) →
   copiala completa. Se ve así:
   ```
   postgresql://usuario:password@ep-xxxx-pooler.us-east-1.aws.neon.tech/neondb?sslmode=require
   ```
4. Guardala — es tu `DB_URL`. Si querés, renombrá la base de `neondb` a
   `bandek_db` desde el dashboard (opcional, cosmético).

## 2. Cloudflare R2 (imágenes)

1. Creá cuenta en [Cloudflare](https://dash.cloudflare.com) (sin tarjeta
   para el free tier de R2).
2. **R2 Object Storage** → **Create bucket** → nombre `bandek-imagenes`.
3. Adentro del bucket, pestaña **Settings** → **Public Access** → activalo
   (necesario: las imágenes del catálogo son públicas). Copiá la URL
   `https://pub-xxxxxxxx.r2.dev` que te da — es tu `AWS_URL`.
4. **R2 → Manage API Tokens → Create API Token**: permisos *Object Read &
   Write*, alcance limitado al bucket `bandek-imagenes`. Te da:
   - `Access Key ID` → `AWS_ACCESS_KEY_ID`
   - `Secret Access Key` → `AWS_SECRET_ACCESS_KEY`
5. El **endpoint** de la cuenta (aparece en la misma pantalla de R2, o en
   Overview) tiene esta forma:
   ```
   https://<ACCOUNT_ID>.r2.cloudflarestorage.com
   ```
   → es tu `AWS_ENDPOINT`.
6. `AWS_BUCKET=bandek-imagenes`, `AWS_DEFAULT_REGION=auto`,
   `AWS_USE_PATH_STYLE_ENDPOINT=true`.

## 3. Resend (correo) — verificar el dominio

El `.env` actual usa `onboarding@resend.dev`, que es una dirección de
prueba de Resend (limitada). Para producción:

1. En Resend → **Domains** → **Add Domain** → tu dominio real.
2. Agregá los registros DNS (SPF/DKIM) que te muestra donde tengas el
   dominio comprado.
3. Una vez verificado, `MAIL_FROM_ADDRESS` puede ser
   `notificaciones@tu-dominio.com`.

Si todavía no tenés dominio propio, podés arrancar con
`onboarding@resend.dev` (solo llegan los correos a la dirección con la que
creaste la cuenta de Resend) y cambiarlo después.

## 4. Render (el servidor)

1. Subí los commits de este cambio a GitHub (`git push`).
2. En [render.com](https://render.com) → **New → Blueprint** → conectá el
   repo `Pablopalaci0s/ferreteria-bandek`. Render lee [render.yaml](render.yaml)
   solo y arma el servicio.
3. Te va a pedir valor para cada variable marcada `sync: false` en
   render.yaml. Completalas con lo juntado en los pasos 1-3 (usá
   [.env.cloud.example](.env.cloud.example) como referencia de qué va en
   cada una). `APP_KEY` y `CRON_SECRET` se generan solos.
4. **Create New Resources** → Render clona el repo, construye la imagen
   Docker (usa [Dockerfile](Dockerfile)) y la despliega. La primera build
   tarda varios minutos.
5. Cuando termine, `php artisan migrate --force` ya corrió (está en el
   `CMD` del Dockerfile) — la base de Neon ya tiene las tablas.
6. Entrá a la URL que te da Render (`https://bandek.onrender.com` o
   similar) y confirmá que carga. Iniciá sesión como admin y subí una
   imagen de prueba a un producto — si aparece y termina en `.webp`,
   R2 está bien conectado.

**Sin Blueprint**: si preferís armarlo a mano en vez de usar render.yaml,
elegí **New → Web Service → Docker**, apuntá al repo, y cargá las mismas
variables una por una desde [.env.cloud.example](.env.cloud.example).

## 5. GitHub Actions (cron del backup diario)

1. En el repo de GitHub → **Settings → Secrets and variables → Actions**
   → **New repository secret**:
   - `APP_URL` = la URL de Render (sin `/` al final)
   - `CRON_SECRET` = el mismo valor que quedó configurado en Render
     (Render → tu servicio → Environment → `CRON_SECRET`, copiá el valor
     que se generó solo)
2. El workflow [.github/workflows/cron-backup.yml](.github/workflows/cron-backup.yml)
   ya está en el repo — corre solo todos los días a las 03:00 (hora de El
   Salvador). Probalo a mano: pestaña **Actions** → **Backup diario** →
   **Run workflow**.
3. *(Opcional)* [.github/workflows/keep-alive.yml](.github/workflows/keep-alive.yml)
   le hace ping al sitio cada 10 min para evitar el cold-start de 30-60s
   tras 15 min sin visitas. Usa el mismo secret `APP_URL`. Borralo si no
   te importa esa demora ocasional.

## 6. Verificación final

- [ ] El sitio carga por HTTPS (Render lo da automático).
- [ ] `APP_DEBUG=false` — probá una URL rota y confirmá que NO se ve una
      traza de error de Laravel.
- [ ] Subir una imagen de producto/banner y que quede en `.webp` (confirma
      R2).
- [ ] **Actions → Backup diario → Run workflow** a mano una vez, y
      confirmá en Google Drive que apareció el archivo `.sql.gz`.
- [ ] Restablecer la contraseña de un usuario de prueba y confirmar que el
      correo llega (confirma Resend).
- [ ] Esperar ~20 min sin tocar el sitio y volver a entrar — si no
      activaste el keep-alive, la primera carga va a tardar unos segundos
      (cold start). Es esperable, no es un error.

## Límites a tener en cuenta (por si el sitio crece)

- **Neon free**: 100 CU-hours/mes, 0.5 GB de datos. Para un catálogo de
  ferretería con tráfico bajo/medio alcanza cómodo; si se llena, el
  siguiente paso es el plan pago de Neon (~$19/mes) o mover a un Postgres
  propio en el VPS de [DEPLOY.md](DEPLOY.md).
- **R2 free**: 10 GB de imágenes. Un catálogo de varios cientos de
  productos con fotos optimizadas a WebP entra sin problema.
- **Render free**: 750h/mes (alcanza para 1 servicio 24/7), pero SIN el
  keep-alive el servicio duerme tras 15 min de inactividad — cold start de
  30-60s en la siguiente visita. No hay SLA ni soporte prioritario en free.
- Si el negocio crece y esto empieza a quedarse corto, [DEPLOY.md](DEPLOY.md)
  tiene la ruta a un VPS propio de ~$5/mes sin estas limitaciones.
