# Guía de Despliegue con Docker - Nexora Arena
> Pasos para lanzar el proyecto completo en cualquier servidor con Docker.

---

## Requisitos del servidor

- Docker y Docker Compose instalados
- Al menos **20GB de disco**
- Al menos **1GB RAM + 2GB swap** (o 2GB RAM)
- Puertos abiertos: 8000 (app), 8080 (phpMyAdmin), 3306 (MySQL)

---

## Archivos necesarios en el repositorio

Estos archivos ya están incluidos en el repositorio:

| Archivo | Descripción |
|---|---|
| `Dockerfile` | Imagen de Laravel con PHP 8.3, Composer y Node 20 |
| `docker-compose.prod.yml` | Orquesta Laravel + MySQL 8.4 + phpMyAdmin |
| `.dockerignore` | Excluye node_modules, vendor, .git, etc. |

---

## Paso 1 — Clonar el repositorio

```bash
git clone https://github.com/Mayis2807/Nexora-Arena.git nexora-arena
cd nexora-arena
```

---

## Paso 2 — Añadir swap memory (si la máquina tiene poca RAM)

Necesario en servidores con 1GB RAM como AWS t2.micro:

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
free -h  # Verificar que muestra 2GB swap
```

---

## Paso 3 — Levantar los contenedores

```bash
APP_KEY="base64:Qq/RnAOtM1XJprL9wvTZICLxwlxFCPpDwopPkdJ40Os=" docker compose -f docker-compose.prod.yml up -d --build
```

La primera vez tardará varios minutos porque descarga las imágenes y compila los assets.

Verificar que los contenedores están corriendo:
```bash
docker compose -f docker-compose.prod.yml ps
```

Deberías ver tres contenedores en estado `Up`:
- `nexora-arena-app-1` → Laravel en puerto 8000
- `nexora-arena-mysql-1` → MySQL en puerto 3306
- `nexora-arena-phpmyadmin-1` → phpMyAdmin en puerto 8080

---

## Paso 4 — Ejecutar migraciones y seeders

```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate:fresh --seed
```

Cuando pregunte confirmación (modo production) escribir `yes`.

---

## Paso 5 — Acceder a la aplicación

| Servicio | URL | Usuario | Contraseña |
|---|---|---|---|
| Aplicación | `http://IP_DEL_SERVIDOR:8000` | — | — |
| phpMyAdmin | `http://IP_DEL_SERVIDOR:8080` | nexora_user | NexoraPass2024!Student |
| Admin app | `/admin` | admin@roigarena.com | admin123 |
| Usuario normal | — | usuario@roigarena.com | password123 |

---

## Comandos útiles

| Acción | Comando |
|---|---|
| Ver logs de Laravel | `docker compose -f docker-compose.prod.yml logs app` |
| Ver logs de MySQL | `docker compose -f docker-compose.prod.yml logs mysql` |
| Entrar al contenedor Laravel | `docker compose -f docker-compose.prod.yml exec app bash` |
| Parar todos los contenedores | `docker compose -f docker-compose.prod.yml down` |
| Parar y borrar datos BD | `docker compose -f docker-compose.prod.yml down -v` |
| Reconstruir imagen | `docker compose -f docker-compose.prod.yml up -d --build` |
| Limpiar imágenes sin usar | `docker system prune -af` |

---

## Actualizar el proyecto

Cuando haya cambios en GitHub:

```bash
git pull origin main
docker compose -f docker-compose.prod.yml up -d --build
```

Si solo cambiaron archivos PHP (sin assets):
```bash
git pull origin main
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
```

---

## Solución de problemas

| Problema | Causa | Solución |
|---|---|---|
| Build cancelado | Poca RAM | Añadir swap memory (Paso 2) |
| Disco lleno | Imágenes Docker acumuladas | `docker system prune -af` |
| Contenedor app no arranca | Error en .env o APP_KEY | Verificar variables de entorno |
| MySQL no conecta | Contenedor aún iniciando | Esperar 20-30 segundos y reintentar |
| Puerto 8000 no accesible | Firewall/Security Group | Abrir puerto 8000 en las reglas de entrada |