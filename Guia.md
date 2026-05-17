# Guía Real de Configuración - Proyecto Nexora Arena

## Entorno
- **OS:** Windows 11 con WSL2 (Ubuntu)
- **PHP:** 8.3.6 (local) / 8.3.31 (EC2)
- **Laravel:** 13.7.0
- **Docker Desktop:** 29.4.1
- **Sanctum:** v4.3.1
- **Bootstrap:** 5.3
- **AWS EC2:** Ubuntu 22.04 LTS, t2.micro
- **MySQL:** 8.4 (Docker en EC2)

---

## FASE 1: INSTALACIÓN LOCAL

### 1.1 Instalar Docker Desktop

Ejecutar el instalador desde PowerShell como administrador:
```powershell
Start-Process "C:\Users\mppen\Downloads\Docker Desktop Installer.exe" -Verb RunAs -Wait
```

Después de instalar, activar la integración con WSL:
- Abrir Docker Desktop → Settings ⚙️ → Resources → WSL Integration
- Activar => "Enable integration with my default WSL distro"
- Activar => Ubuntu
- Clic en "Apply & Restart"

Verificar que funciona desde WSL:
```bash
docker --version
# Docker version 29.4.1, build 055a478
```

---

### 1.2 Instalar Laravel Sail

Desde WSL, instalar curl si no está disponible:
```bash
sudo apt update && sudo apt install curl -y
```

Crear el proyecto con PHP 8.3:
```bash
curl -s "https://laravel.build/roig-arena?php=83&with=mysql" | bash
cd roig-arena
```

**Problema encontrado:** El Dockerfile intentaba instalar PHP 8.5 que no existe en los repositorios, y no podía conectar.

**Solución:** Reemplazar el Dockerfile de 8.3 con una versión simplificada:
```bash
cat > ~/roig-arena/vendor/laravel/sail/runtimes/8.3/Dockerfile << 'EOF'
FROM ubuntu:24.04
LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=20
ARG MYSQL_CLIENT="default-mysql-client"
ARG POSTGRES_VERSION=15

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV LANG=C.UTF-8
ENV SUPERVISOR_PHP_USER="sail"

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get upgrade -y \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils nano \
    && apt-get install -y \
        php8.3-cli php8.3-dev \
        php8.3-mysql php8.3-mbstring \
        php8.3-xml php8.3-zip php8.3-bcmath \
        php8.3-intl php8.3-readline \
        php8.3-curl php8.3-gd \
        php8.3-sqlite3 \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get install -y nodejs npm \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.3
RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.3/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80/tcp
ENTRYPOINT ["start-container"]
EOF
```

**Problema encontrado:** El `supervisord.conf` causaba el error:
> `You should set SUPERVISOR_PHP_USER to either 'sail' or 'root'`

**Solución:** Reemplazar el supervisord.conf:
```bash
cat > ~/roig-arena/vendor/laravel/sail/runtimes/8.3/supervisord.conf << 'EOF'
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php]
command=/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80
user=sail
environment=LARAVEL_SAIL="1"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF
```

Crear el `docker-compose.yml` manualmente:
```bash
cat > ~/roig-arena/docker-compose.yml << 'EOF'
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.3
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: sail-8.3/app
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
            IGNITION_LOCAL_SITES_PATH: '${PWD}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - mysql
    mysql:
        image: 'mysql/mysql-server:8.0'
        ports:
            - '${FORWARD_DB_PORT:-3306}:3306'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ROOT_HOST: '%'
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
            MYSQL_ALLOW_EMPTY_PASSWORD: 1
        volumes:
            - 'sail-mysql:/var/lib/mysql'
        networks:
            - sail
        healthcheck:
            test:
                - CMD
                - mysqladmin
                - ping
                - '-p${DB_PASSWORD}'
            retries: 3
            timeout: 5s
    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        environment:
            PMA_HOST: mysql
            PMA_PORT: 3306
        ports:
            - '8081:80'
        networks:
            - sail
        depends_on:
            - mysql
networks:
    sail:
        driver: bridge
volumes:
    sail-mysql:
        driver: local
EOF
```

Arreglar permisos y arrancar:
```bash
chmod -R 777 ~/roig-arena/storage
chmod -R 777 ~/roig-arena/bootstrap/cache
chmod -R 777 ~/roig-arena/database
sail root-shell
chmod -R 777 /var/www/html
exit
./vendor/bin/sail down -v
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d
```

✅ Laravel: http://localhost:8000
✅ phpMyAdmin: http://localhost:8081 (usuario: `sail`, contraseña: `password`)

---

### 1.3 Instalar Bootstrap y Vite

```bash
sail npm install bootstrap @popperjs/core
sail npm install
```

En `resources/js/app.js`:
```js
import './bootstrap';
import 'bootstrap';
```

En `resources/css/app.css`:
```css
@import 'bootstrap/dist/css/bootstrap.min.css';
```

---

### 1.4 Alias de Sail

```bash
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc
```

---

### 1.5 Comandos útiles

| Acción | Comando |
|---|---|
| Iniciar servicios | `sail up -d` |
| Detener servicios | `sail down` |
| Detener y borrar volúmenes | `sail down -v` |
| Ejecutar migraciones | `sail artisan migrate` |
| Rehacer todas las migraciones | `sail artisan migrate:fresh --seed` |
| Crear modelo | `sail artisan make:model NombreModelo` |
| Crear migración | `sail artisan make:migration create_tabla_table` |
| Crear controlador | `sail artisan make:controller NombreController` |
| Crear resource | `sail artisan make:resource NombreResource` |
| Crear middleware | `sail artisan make:middleware NombreMiddleware` |
| Crear comando | `sail artisan make:command NombreCommand` |
| Crear factory | `sail artisan make:factory NombreFactory` |
| Crear test feature | `sail artisan make:test NombreTest` |
| Crear test unit | `sail artisan make:test NombreTest --unit` |
| Ejecutar tests | `sail artisan test` |
| Consola interactiva | `sail artisan tinker` |
| Entrar al contenedor | `sail bash` |
| Entrar como root | `sail root-shell` |
| Limpiar caché | `sail artisan optimize:clear` |
| Ver rutas | `sail artisan route:list --path=api` |
| Dump autoload | `sail composer dump-autoload` |
| Liberar reservas | `sail artisan reservas:liberar` |

---

## FASE 2: BASE DE DATOS

### Migración de users
Archivo: `database/migrations/0001_01_01_000000_create_users_table.php`

Campos: `nombre`, `apellido`, `email`, `password`, `is_admin`, `softDeletes`

### Migración principal
Archivo: `database/migrations/XXXX_create_roig_arena_tables.php`

Tablas en orden: `sectores` → `asientos` → `eventos` → `precios` → `estado_asientos` → `entradas`

```bash
sail artisan migrate:fresh --seed
```

---

## FASE 3: MODELOS

| Modelo | Características especiales |
|---|---|
| `User.php` | HasApiTokens, SoftDeletes, relaciones reservas/entradas, isAdmin() |
| `Sector.php` | scope activos, asientosDisponiblesParaEvento() |
| `Asiento.php` | estaDisponible/Reservado/VendidoParaEvento() |
| `Evento.php` | SoftDeletes, scopes futuros/pasados/delMes |
| `Precio.php` | estaDisponible() combina sector.activo + precio.disponible |
| `EstadoAsiento.php` | tiempoRestante(), haExpirado(), scope expirados |
| `Entrada.php` | generación automática de QR en boot() |

---

## FASE 4: AUTENTICACIÓN

```bash
sail composer require laravel/sanctum
sail artisan install:api
```

| Ruta | Método | Descripción |
|---|---|---|
| `/api/register` | POST | Registro de usuario |
| `/api/login` | POST | Inicio de sesión |
| `/api/logout` | POST | Cierre de sesión |
| `/api/user` | GET | Usuario autenticado |

---

## FASE 5-8: CONTROLADORES, RESOURCES, SERVICES Y MIDDLEWARE

### Estructura de archivos

```
app/
├── Console/Commands/
│   └── LiberarReservasExpiradas.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/AuthController.php
│   │   ├── EventoController.php
│   │   ├── SectorController.php
│   │   ├── AsientoController.php
│   │   ├── ReservaController.php
│   │   ├── CompraController.php
│   │   ├── EntradaController.php
│   │   └── Web/
│   │       ├── HomeController.php
│   │       ├── EventoWebController.php
│   │       ├── AuthWebController.php
│   │       ├── EntradaWebController.php
│   │       ├── ReservaWebController.php
│   │       ├── CompraWebController.php
│   │       └── AdminController.php
│   ├── Middleware/
│   │   └── IsAdmin.php
│   └── Resources/
│       ├── UserResource.php
│       ├── EventoResource.php
│       ├── SectorResource.php
│       ├── AsientoResource.php
│       ├── PrecioResource.php
│       ├── ReservaResource.php
│       └── EntradaResource.php
└── Services/
    ├── ReservaService.php
    ├── CompraService.php
    └── LiberarReservasService.php
```

⚠️ **Los Resources van en `app/Http/Resources/` NO en `resources/`**

### Registrar middleware en bootstrap/app.php

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
    $middleware->alias([
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})
```

### Comando programado en routes/console.php

```php
Schedule::command('reservas:liberar')->everyMinute();
```

---

## FASE 9: RUTAS

### API (routes/api.php) — 21 rutas

```
PÚBLICAS:
POST   /api/register
POST   /api/login
GET    /api/eventos
GET    /api/eventos/{id}
GET    /api/eventos/{eventoId}/asientos
GET    /api/eventos/{eventoId}/sectores/{sectorId}/asientos

PROTEGIDAS (auth:sanctum):
GET    /api/user
POST   /api/logout
GET    /api/reservas
POST   /api/reservas
DELETE /api/reservas/{id}
POST   /api/compra
GET    /api/entradas
GET    /api/entradas/{id}
POST   /api/web/reservas  ← Para el selector de asientos web

ADMIN (auth:sanctum + admin):
POST   /api/admin/eventos
PUT    /api/admin/eventos/{id}
DELETE /api/admin/eventos/{id}
POST   /api/admin/sectores
PUT    /api/admin/sectores/{id}
DELETE /api/admin/sectores/{id}
```

### Web (routes/web.php)

```
GET    /                           → HomeController@index
GET    /eventos                    → EventoWebController@index
GET    /eventos/{id}               → EventoWebController@show
GET    /login                      → AuthWebController@showLogin
POST   /login                      → AuthWebController@login
GET    /register                   → AuthWebController@showRegister
POST   /register                   → AuthWebController@register
POST   /logout                     → AuthWebController@logout

AUTH REQUERIDO:
GET    /mis-entradas               → EntradaWebController@index
GET    /mis-entradas/{id}          → EntradaWebController@show
GET    /carrito                    → ReservaWebController@index
POST   /reservas                   → ReservaWebController@store
DELETE /reservas/{id}              → ReservaWebController@destroy
POST   /compra                     → CompraWebController@store
GET    /compra/confirmacion        → CompraWebController@confirmacion

ADMIN REQUERIDO:
GET    /admin                      → AdminController@index
GET    /admin/eventos              → AdminController@eventos
GET    /admin/eventos/crear        → AdminController@crearEvento
POST   /admin/eventos              → AdminController@storeEvento
GET    /admin/eventos/{id}/editar  → AdminController@editarEvento
PUT    /admin/eventos/{id}         → AdminController@updateEvento
DELETE /admin/eventos/{id}         → AdminController@destroyEvento
GET    /admin/sectores             → AdminController@sectores
POST   /admin/sectores             → AdminController@storeSector
PUT    /admin/sectores/{id}        → AdminController@updateSector
DELETE /admin/sectores/{id}        → AdminController@destroySector
GET    /admin/usuarios             → AdminController@usuarios
```

---

## FASE 10: SEEDERS

```bash
sail artisan make:seeder SectorSeeder
sail artisan make:seeder AsientoSeeder
sail artisan make:seeder UserSeeder
sail artisan make:seeder EventoSeeder
sail artisan make:seeder PrecioSeeder
sail artisan make:seeder EstadoAsientoSeeder
```

### Credenciales de usuarios

| Usuario | Email | Contraseña | Rol |
|---|---|---|---|
| Admin Sistema | admin@roigarena.com | admin123 | Administrador |
| Juan García | usuario@roigarena.com | password123 | Usuario |

### Datos generados

| Tabla | Registros |
|---|---|
| sectores | 71 |
| asientos | 14.896 |
| usuarios | 2 |
| eventos | 5 |
| precios | 284 |
| estado_asientos | ~5.000 (prueba) |

---

## FASE 11: COMANDO PROGRAMADO

```bash
sail artisan make:command LiberarReservasExpiradas
```

Probar manualmente:
```bash
sail artisan reservas:liberar
```

---

## FASE 12: FACTORIES

```bash
sail artisan make:factory SectorFactory --model=Sector
sail artisan make:factory AsientoFactory --model=Asiento
sail artisan make:factory EventoFactory --model=Evento
sail artisan make:factory PrecioFactory --model=Precio
sail artisan make:factory EstadoAsientoFactory --model=EstadoAsiento
sail artisan make:factory EntradaFactory --model=Entrada
```

---

## FASE 13: TESTS

### Configurar phpunit.xml para SQLite en memoria

Añadir dentro de `<php>`:
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="LOG_CHANNEL" value="null"/>
```

⚠️ `LOG_CHANNEL=null` es necesario para evitar errores de permisos al escribir logs durante los tests cuando la BD está en remoto.

### Crear tests

```bash
sail artisan make:test AuthTest
sail artisan make:test EventoTest
sail artisan make:test ReservaTest
sail artisan make:test CompraTest
sail artisan make:test ModeloTest --unit
sail artisan make:test ReservaServiceTest --unit
sail artisan make:test CompraServiceTest --unit
sail artisan make:test LiberarReservasServiceTest --unit
```

### Ejecutar tests

```bash
sail artisan test
sail artisan test --verbose
```

---

## FRONTEND BLADE

### Estructura de vistas

```
resources/views/
├── layouts/
│   └── app.blade.php
└── web/
    ├── home.blade.php
    ├── auth/
    │   ├── login.blade.php
    │   └── register.blade.php
    ├── eventos/
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── entradas/
    │   ├── index.blade.php
    │   └── show.blade.php
    ├── carrito/
    │   └── index.blade.php
    ├── compra/
    │   └── confirmacion.blade.php
    └── admin/
        ├── index.blade.php
        ├── eventos.blade.php
        ├── eventos-crear.blade.php
        ├── eventos-editar.blade.php
        ├── sectores.blade.php
        └── usuarios.blade.php
```

### Paleta de colores

```css
:root {
    --bg-primary: #0A0A0F;
    --bg-surface: #12121A;
    --bg-card: #1A1A28;
    --color-purple: #9D50FF;
    --color-orange: #FF7B00;
    --color-gold: #D4AF37;
    --color-gold-light: #F9E29B;
    --text-muted-custom: #7A7A9A;
}
```

### Imágenes

Las imágenes van en `public/imagenes/`:
- `nexora-logo.png` — Logo del navbar
- `nexora-arena1.png` — Imagen hero
- `inf.png` — Sala Infinito
- `aventura.png` — Poster evento concierto
- `baloncesto.png` — Poster evento baloncesto

### Funcionalidades JS destacadas

**Selector de asientos:**
- Selección múltiple arrastrando (máx. 5 asientos)
- Colores por disponibilidad: verde >50%, naranja 20-50%, rojo <20%, gris agotado
- Reserva secuencial para evitar deadlocks en MySQL

**Carrito:**
- Temporizador de cuenta regresiva (30 min)
- Recarga automática al expirar

**Registro:**
- Pregunta anti-bot tipo test con 15 preguntas sobre el estadio
- Selección por radio buttons

### Notas importantes

- Los Resources van en `app/Http/Resources/` NO en `resources/`
- Añadir `<meta name="csrf-token">` en el layout para las peticiones fetch
- Bootstrap se carga via Vite, no por CDN
- Bootstrap Icons se carga por CDN
- Las reservas se hacen de forma secuencial con `async/await` para evitar deadlocks

---

## FASE 14: DESPLIEGUE EN AWS (Base de datos remota)

### Arquitectura

```
Tu máquina (desarrollo)          AWS EC2
├── Contenedor Laravel  <-->     ├── Contenedor MySQL 8.4
                                 └── Contenedor phpMyAdmin
```

### 14.1 Modificar compose.yaml local

Eliminar los servicios MySQL y phpMyAdmin del `docker-compose.yml` local, dejando solo Laravel:

```yaml
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.3
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: sail-8.3/app
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
            IGNITION_LOCAL_SITES_PATH: '${PWD}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
networks:
    sail:
        driver: bridge
```

### 14.2 Crear compose-database-server.yaml

Crear en la raíz del proyecto (se subirá a EC2):

```yaml
services:
  mysql:
    image: 'mysql:8.4'
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: 'RootPass2024!Secure'
      MYSQL_ROOT_HOST: '%'
      MYSQL_DATABASE: 'nexora_arena'
      MYSQL_USER: 'nexora_user'
      MYSQL_PASSWORD: 'NexoraPass2024!Student'
      MYSQL_ALLOW_EMPTY_PASSWORD: 0
    volumes:
      - 'mysql_data:/var/lib/mysql'
    restart: unless-stopped
    healthcheck:
      test:
        - CMD
        - mysqladmin
        - ping
        - '-pNexoraPass2024!Student'
      retries: 3
      timeout: 5s

  phpmyadmin:
    image: 'phpmyadmin:latest'
    ports:
      - '8080:80'
    environment:
      PMA_HOST: mysql
      PMA_USER: 'nexora_user'
      PMA_PASSWORD: 'NexoraPass2024!Student'
      UPLOAD_LIMIT: 300M
    restart: unless-stopped
    depends_on:
      - mysql

volumes:
  mysql_data:
    driver: local
```

### 14.3 Credenciales de la base de datos remota

| Parámetro | Valor |
|---|---|
| Base de datos | `nexora_arena` |
| Usuario | `nexora_user` |
| Contraseña | `NexoraPass2024!Student` |

### 14.4 Configurar AWS EC2

**Key Pair:**
1. EC2 → Network & Security → Key Pairs → Create key pair
2. Name: `nexora-db-key`, tipo RSA, formato `.pem`
3. Mover a WSL y dar permisos:

```bash
mkdir -p ~/.ssh
mv ~/Downloads/nexora-db-key.pem ~/.ssh/
chmod 400 ~/.ssh/nexora-db-key.pem
```

**Security Group (`nexora-database-sg`):**

| Type | Port | Source | Descripción |
|---|---|---|---|
| SSH | 22 | My IP | Acceso SSH |
| MySQL/Aurora | 3306 | My IP | Base de datos |
| Custom TCP | 8080 | My IP | phpMyAdmin |
| Custom TCP | 8000 | Anywhere (0.0.0.0/0) | Laravel app pública |

⚠️ Si tu IP cambia (por ejemplo al día siguiente), hay que actualizar las reglas SSH y 3306 con "My IP" de nuevo.

**Instancia EC2:**
- AMI: Ubuntu Server 22.04 LTS (Free tier eligible)
- Instance type: t2.micro
- Key pair: `nexora-db-key`
- Security group: `nexora-database-sg`
- Storage: 8 GiB

**Elastic IP (IP fija):**

Para evitar que la IP cambie al reiniciar la instancia, asignar una Elastic IP:
1. EC2 → Elastic IPs → Allocate Elastic IP address
2. Seleccionar la IP → Actions → Associate Elastic IP address
3. Seleccionar la instancia `nexora-database-server`

IP fija del proyecto: `54.226.167.18`

### 14.5 Instalar Docker en EC2

```bash
ssh -i ~/.ssh/nexora-db-key.pem ubuntu@54.226.167.18

sudo apt update && sudo apt upgrade -y
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update && sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
sudo usermod -aG docker ubuntu
exit

# Reconectar y verificar
ssh -i ~/.ssh/nexora-db-key.pem ubuntu@54.226.167.18
docker --version        # Docker version 29.5.0
docker compose version  # Docker Compose version v5.1.3
```

### 14.6 Subir y lanzar la base de datos en EC2

Desde la máquina local:
```bash
scp -i ~/.ssh/nexora-db-key.pem ~/nexora-arena/compose-database-server.yaml ubuntu@54.226.167.18:~/
```

Desde EC2:
```bash
docker compose -f compose-database-server.yaml up -d
docker compose -f compose-database-server.yaml ps
```

✅ phpMyAdmin: http://54.226.167.18:8080

### 14.7 Configurar .env local para BD remota

```env
DB_CONNECTION=mysql
DB_HOST=54.226.167.18
DB_PORT=3306
DB_DATABASE=nexora_arena
DB_USERNAME=nexora_user
DB_PASSWORD=NexoraPass2024!Student
```

---

## FASE 15: OPTIMIZACIÓN DE SEEDERS PARA BD REMOTA

Con la BD en AWS, los seeders con `Model::create()` individual son extremadamente lentos (~horas) por la latencia de red. Solución: usar `Model::insert()` por lotes.

### Comparativa

| Método | Queries | Velocidad |
|---|---|---|
| `create()` en loop | 1 por registro | Horas con BD remota |
| `insert()` por lotes | 1 por lote de 1000 | Segundos |

### Reglas con insert()

1. Añadir `created_at` y `updated_at` manualmente (no se generan solos)
2. Usar el mismo `$now = now()` para todo el lote
3. Para >10.000 registros usar chunks de 1000

```php
// ✅ Patrón correcto
$now = now();
$batch = [];

foreach ($items as $item) {
    $batch[] = [
        'campo' => $item->valor,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    if (count($batch) >= 1000) {
        Model::insert($batch);
        $batch = [];
    }
}

if (!empty($batch)) {
    Model::insert($batch);
}
```

### Resultados obtenidos

| Seeder | Antes | Después |
|---|---|---|
| AsientoSeeder (~15.000) | Horas | ~5-10 segundos |
| PrecioSeeder (~280) | Minutos | <1 segundo |
| SectorSeeder (~71) | Segundos | <1 segundo |
| EstadoAsientoSeeder (~7.000) | Horas | Segundos |

---

## FASE 16: SCRIPT DE AUTOMATIZACIÓN

El script `arena2.sh` se coloca en la carpeta **padre** de `nexora-arena` y automatiza todo el proceso de reinicio.

```bash
#!/bin/bash

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${BLUE}>>> Iniciando proceso de reinicio del entorno Nexora Arena (DB remota en AWS)...${NC}\n"

# 1. Levantar base de datos en AWS
echo -e "${YELLOW}[1/5] Levantando base de datos en AWS...${NC}"
ssh -i ~/.ssh/nexora-db-key.pem ubuntu@54.226.167.18 "docker compose -f compose-database-server.yaml up -d"
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✔ Base de datos en AWS iniciada.${NC}"
    echo -n "Esperando"
    for i in {1..20}; do echo -n "."; sleep 1; done
    echo ""
    echo -e "${GREEN}✔ MySQL listo.${NC}\n"
else
    echo "Error al iniciar base de datos en AWS"; exit 1
fi

# 2. Acceder al directorio del proyecto
cd nexora-arena || { echo "Error: No se pudo encontrar la carpeta 'nexora-arena'"; exit 1; }

# 3. Detener contenedores locales
echo -e "${YELLOW}[2/5] Deteniendo contenedores locales...${NC}"
./vendor/bin/sail down
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✔ Contenedores detenidos correctamente.${NC}\n"
else
    echo "Error al detener contenedores"; exit 1
fi

# 4. Iniciar contenedores
echo -e "${YELLOW}[3/5] Levantando servicios en modo detach...${NC}"
./vendor/bin/sail up -d
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✔ Servicios iniciados.${NC}\n"
else
    echo "Error al iniciar Sail"; exit 1
fi

# 5. Migraciones y seeders
echo -e "${YELLOW}[4/5] Ejecutando migraciones y poblando base de datos remota...${NC}"
./vendor/bin/sail artisan migrate:fresh --seed
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✔ Base de datos remota lista y poblada.${NC}\n"
else
    echo "Error en las migraciones"; exit 1
fi

# 6. Tests
echo -e "${YELLOW}[5/5] Ejecutando batería de tests...${NC}"
./vendor/bin/sail artisan test
if [ $? -eq 0 ]; then
    echo -e "\n${GREEN}🚀 TODO CORRECTO: Entorno reiniciado, poblado y testeado con éxito.${NC}"
else
    echo -e "\n${YELLOW}⚠ Los servicios están arriba pero algunos tests han fallado.${NC}"
    exit 1
fi
```

Dar permisos y ejecutar:
```bash
chmod +x nexora-arena.sh
./nexora-arena.sh
```

⚠️ Docker Desktop debe estar corriendo antes de ejecutar el script.

---

## FASE 17: DESPLIEGUE COMPLETO DE LARAVEL EN EC2

Para que la app sea accesible públicamente sin depender de la máquina local.

### Arquitectura final

```
AWS EC2 (54.226.167.18)
├── Contenedor MySQL 8.4     → puerto 3306
├── Contenedor phpMyAdmin    → puerto 8080
└── Laravel (php artisan serve) → puerto 8000
```

### 17.1 Subir el proyecto

Desde la máquina local (excluyendo carpetas pesadas):
```bash
cd ~
tar --exclude='nexora-arena/node_modules' \
    --exclude='nexora-arena/vendor' \
    --exclude='nexora-arena/.git' \
    --exclude='nexora-arena/storage/logs' \
    -czf nexora-arena.tar.gz nexora-arena

scp -i ~/.ssh/nexora-db-key.pem nexora-arena.tar.gz ubuntu@54.226.167.18:~/
```

Desde EC2:
```bash
ssh -i ~/.ssh/nexora-db-key.pem ubuntu@54.226.167.18
tar -xzf nexora-arena.tar.gz
cd nexora-arena
```

### 17.2 Instalar PHP 8.3 en EC2

**Problema encontrado:** El PPA de Ondrej (`ppa.launchpadcontent.net`) no funciona en esta versión de Ubuntu. Usar el repositorio alternativo de sury.org:

```bash
# Usar repositorio alternativo:
sudo apt install -y lsb-release ca-certificates apt-transport-https
sudo curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
sudo sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -cs) main" > /etc/apt/sources.list.d/php.list'
sudo apt update
sudo apt install -y php8.3-cli php8.3-mbstring php8.3-xml php8.3-zip php8.3-bcmath php8.3-curl php8.3-mysql php8.3-sqlite3 php8.3-gd unzip

php --version  # PHP 8.3.31
```

### 17.3 Instalar Composer

```bash
curl -sLS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer install --no-dev --optimize-autoloader
```

### 17.4 Instalar Node.js 20 y compilar assets

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
node --version  # v20.20.2

npm install
npm run build
```

### 17.5 Configurar entorno en EC2

```bash
chmod -R 775 storage bootstrap/cache
php artisan key:generate
```

Editar `.env`:
```bash
nano .env
```

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://54.226.167.18:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexora_arena
DB_USERNAME=nexora_user
DB_PASSWORD=NexoraPass2024!Student
```

⚠️ `DB_HOST=127.0.0.1` porque MySQL corre en la misma instancia EC2, sin latencia de red.

### 17.6 Ejecutar migraciones y arrancar

```bash
php artisan migrate:fresh --seed
php artisan serve --host=0.0.0.0 --port=8000
```

✅ App pública: http://54.226.167.18:8000
✅ phpMyAdmin: http://54.226.167.18:8080


---

## RESUMEN DE ACCESOS

| Servicio | URL | Usuario | Contraseña |
|---|---|---|---|
| Laravel local | http://localhost:8000 | — | — |
| Laravel en AWS | http://54.226.167.18:8000 | — | — |
| phpMyAdmin AWS | http://54.226.167.18:8080 | nexora_user | NexoraPass2024!Student |
| Admin app | /admin | admin@roigarena.com | admin123 |
| Usuario normal | — | usuario@roigarena.com | password123 |