# Desplegar BANDEK a producción (VPS económico)

Guía para poner el sitio en un VPS propio administrado a mano — es la
opción más barata (no hay mensualidad de un panel tipo Forge, solo el
VPS). Con tráfico bajo alcanza de sobra el droplet/instancia más chica
de cualquier proveedor (Hetzner CX22, DigitalOcean Basic, Vultr, etc.):
1 vCPU, 1-2 GB RAM, ~$4-6 USD/mes. Sumale el dominio (~$10-15 USD/año).

Asumimos **Ubuntu 22.04 o 24.04** limpio.

## 1. Paquetes del sistema

```bash
sudo apt update && sudo apt upgrade -y

sudo apt install -y nginx mysql-server git unzip curl \
    software-properties-common

# PHP 8.3 (Ubuntu 24.04 ya lo trae; en 22.04 hace falta el PPA de Ondřej)
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-gd php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-intl php8.3-bcmath \
    php8.3-fileinfo

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node (para compilar los assets con Vite)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

**Verificá GD con WebP** (es lo que arregló el sitio, no es opcional):

```bash
php -m | grep -i gd
php -r "var_dump(function_exists('imagewebp'));"   # debe dar true
```

## 2. Base de datos

```bash
sudo mysql -u root
```

```sql
CREATE DATABASE bandek_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bandek'@'localhost' IDENTIFIED BY 'UNA_CONTRASENA_FUERTE_ACA';
GRANT ALL PRIVILEGES ON bandek_db.* TO 'bandek'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. Traer el código

```bash
sudo mkdir -p /var/www/bandek
sudo chown $USER:$USER /var/www/bandek
git clone https://github.com/Pablopalaci0s/ferreteria-bandek.git /var/www/bandek
cd /var/www/bandek

composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

## 4. Configurar `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con estos valores (el checklist ya está anotado en
[.env.example](.env.example)):

```env
APP_NAME="Ferretería BANDEK"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=bandek_db
DB_USERNAME=bandek
DB_PASSWORD=UNA_CONTRASENA_FUERTE_ACA

SESSION_SECURE_COOKIE=true

MAIL_MAILER=resend
RESEND_API_KEY=re_xxxxxxxxxxxx      # la key REAL de producción, con el dominio verificado en Resend
MAIL_FROM_ADDRESS="notificaciones@tu-dominio.com"
MAIL_FROM_NAME="Ferretería BANDEK"

DB_BACKUP_UPLOAD_DISK=google         # ver BACKUPS.md — sin esto, backup y BD viven en el mismo disco
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REFRESH_TOKEN=
```

## 5. Migrar, enlazar storage, cachear config

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos: nginx/php-fpm (usuario www-data) necesita escribir acá
sudo chown -R www-data:www-data /var/www/bandek/storage /var/www/bandek/bootstrap/cache
sudo find /var/www/bandek/storage -type d -exec chmod 775 {} \;
```

## 6. Nginx

Copiá [deploy/nginx-bandek.conf](deploy/nginx-bandek.conf) a
`/etc/nginx/sites-available/bandek`, reemplazá `tu-dominio.com` por el
dominio real, y activalo:

```bash
sudo cp deploy/nginx-bandek.conf /etc/nginx/sites-available/bandek
sudo ln -s /etc/nginx/sites-available/bandek /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## 7. HTTPS (gratis, Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

Certbot ya deja el renovado automático anotado en su propio cron/timer.

## 8. Cron del scheduler (backup diario a las 03:00)

```bash
crontab -e -u www-data
```

Agregar:

```cron
* * * * * cd /var/www/bandek && php artisan schedule:run >> /dev/null 2>&1
```

Probá el backup a mano una vez:

```bash
sudo -u www-data php artisan db:backup
```

## 9. No hace falta un worker de colas

La app no usa `ShouldQueue` en ningún lado (`QUEUE_CONNECTION=database`
está seteado pero nada lo usa hoy) — no hace falta `supervisor` ni un
proceso de queue corriendo. Si en el futuro se agrega algo con cola,
ahí sí hay que sumar un supervisor/systemd para `queue:work`.

## Para actualizar el sitio (deploys siguientes)

Usá [deploy/deploy.sh](deploy/deploy.sh):

```bash
cd /var/www/bandek && ./deploy/deploy.sh
```

## Checklist final antes de anunciar el sitio

- [ ] `APP_DEBUG=false` (probá una URL que dé 404 o 500 y confirmá que
      NO se ve una traza de error)
- [ ] El sitio carga por `https://` y redirige `http://` a `https://`
- [ ] `php artisan db:backup` corrido a mano una vez y confirmado que
      queda en `storage/app/backups/` (y en Drive si configuraste
      copia off-site)
- [ ] Cron corriendo (`sudo -u www-data crontab -l` lo muestra)
- [ ] Subir una imagen de producto/banner desde el panel y confirmar
      que se guarda como `.webp`
- [ ] Mandar un correo de prueba (resetear la contraseña de un usuario
      de prueba) y confirmar que llega, no cae en spam
