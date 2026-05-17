# Nexora Arena

![Texto alternativo](public/imagenes/nexora-arena1.png)

Sistema backend desarrollado en Laravel para la gestión de eventos, reservas, compra de entradas y control de asientos dentro de una arena o recinto.

---

# Descripción

Nexora Arena es una API REST construida con Laravel que permite:

* Gestión de eventos.
* Administración de sectores y asientos.
* Reserva temporal de asientos.
* Compra de entradas.
* Control de disponibilidad.
* Autenticación mediante Laravel Sanctum.
* Gestión administrativa.
* Liberación automática de reservas expiradas.

El proyecto está pensado como backend para aplicaciones web o móviles enfocadas en venta de entradas para conciertos, deportes, conferencias o cualquier tipo de evento con asientos numerados.

---

# Tecnologías utilizadas

## Backend

* PHP 8.3
* Laravel 13
* Laravel Sanctum
* Eloquent ORM
* SQLite (por defecto)

## Herramientas adicionales

* Composer
* Vite
* Docker Compose
* Laravel Sail

---

# Arquitectura del proyecto

El sistema está organizado siguiendo la arquitectura estándar de Laravel.

## Estructura principal

```bash
app/
├── Console/
│   └── Commands/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Resources/
├── Models/
└── Services/

database/
├── factories/
├── migrations/
└── seeders/

routes/
├── api.php
├── console.php
└── web.php
```

---

# Modelos principales

## Evento

Representa un evento disponible dentro de la plataforma.

Funciones:

* Gestionar sectores disponibles.
* Obtener precios.
* Relacionarse con entradas y reservas.

---

## Sector

Representa una zona dentro del recinto.

Ejemplos:

* VIP
* Preferencial
* General

Cada sector contiene múltiples asientos.

---

## Asiento

Representa un asiento individual.

Contiene:

* Fila
* Número
* Estado
* Disponibilidad por evento

---

## Entrada

Representa una entrada comprada por un usuario.

---

## EstadoAsiento

Controla el estado de cada asiento:

* Disponible
* Reservado
* Comprado

---

## Precio

Gestiona el precio de un sector dependiendo del evento.

---

# Servicios implementados

El proyecto utiliza una capa de servicios para separar la lógica de negocio.

## ReservaService

Encargado de:

* Crear reservas.
* Validar disponibilidad.
* Gestionar expiración.

---

## CompraService

Encargado de:

* Procesar compras.
* Confirmar entradas.
* Cambiar estados de asientos.

---

## LiberarReservasService

Encargado de:

* Liberar reservas expiradas automáticamente.
* Restaurar disponibilidad de asientos.

---

# Comando Artisan personalizado

El proyecto incluye un comando personalizado:

```bash
php artisan reservas:liberar
```

Este comando:

* Busca reservas expiradas.
* Libera los asientos.
* Actualiza los estados automáticamente.

Clase:

```bash
app/Console/Commands/LiberarReservasExpiradas.php
```

---

# Autenticación

La autenticación se realiza mediante Laravel Sanctum.

Endpoints:

```http
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user
```

Las rutas protegidas requieren token Bearer.

Ejemplo:

```http
Authorization: Bearer TU_TOKEN
```

---

# Endpoints principales

## Eventos

### Obtener eventos

```http
GET /api/eventos
```

### Obtener evento por ID

```http
GET /api/eventos/{id}
```

---

## Asientos

### Obtener asientos de un evento

```http
GET /api/eventos/{eventoId}/asientos
```

### Obtener asientos por sector

```http
GET /api/eventos/{eventoId}/sectores/{sectorId}/asientos
```

---

## Reservas

### Obtener reservas

```http
GET /api/reservas
```

### Crear reserva

```http
POST /api/reservas
```

### Cancelar reserva

```http
DELETE /api/reservas/{id}
```

---

## Compra

### Procesar compra

```http
POST /api/compra
```

---

## Entradas

### Obtener entradas

```http
GET /api/entradas
```

### Obtener entrada específica

```http
GET /api/entradas/{id}
```

---

# Rutas administrativas

Las rutas administrativas requieren:

* Usuario autenticado.
* Middleware `admin`.

Prefijo:

```http
/api/admin
```

Funciones:

* Crear eventos.
* Actualizar eventos.
* Eliminar eventos.
* Gestionar sectores.

---

# Instalación del proyecto

## 1. Clonar repositorio

```bash
git clone https://github.com/Mayis2807/Nexora-Arena.git
cd Nexora-Arena
```

---

## 2. Instalar dependencias

```bash
composer install
npm install
```

---

## 3. Configurar entorno

Copiar archivo `.env`:

```bash
cp .env.example .env
```

Generar clave:

```bash
php artisan key:generate
```

---

## 4. Configurar base de datos

El proyecto incluye SQLite por defecto.

Crear archivo:

```bash
touch database/database.sqlite
```

Configurar `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/database/database.sqlite
```

---

## 5. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

---

## 6. Ejecutar servidor

```bash
php artisan serve
```

Servidor:

```bash
http://127.0.0.1:8000
```

---

# Ejecución con Docker

El proyecto incluye:

* `docker-compose.yml`
* `compose-database-server.yaml`

Para iniciar:

```bash
docker compose up -d
```

---

# Desarrollo

Laravel incluye un script preparado:

```bash
composer run dev
```

Esto ejecuta:

* Servidor Laravel.
* Cola de trabajos.
* Logs.
* Vite.

---

# Seeders disponibles

El proyecto incluye seeders para:

* Usuarios
* Eventos
* Sectores
* Asientos
* Precios
* Estados de asientos

Seeder principal:

```bash
DatabaseSeeder
```

---

# Recursos API

El sistema utiliza API Resources de Laravel:

* EventoResource
* AsientoResource
* SectorResource
* EntradaResource
* ReservaResource
* PrecioResource
* UserResource

Esto permite:

* Estandarizar respuestas.
* Ocultar datos sensibles.
* Mejorar serialización JSON.

---

# Middleware personalizado

## IsAdmin

Middleware encargado de validar si el usuario posee permisos administrativos.

Ubicación:

```bash
app/Http/Middleware/IsAdmin.php
```

---

# Posibles mejoras futuras

* Integración con pasarela de pagos.
* WebSockets para actualización en tiempo real.
* Panel administrativo frontend.
* Reportes y analíticas.
* Notificaciones por correo.
* Sistema de reembolsos.

---

# Estado del proyecto

Proyecto funcional en desarrollo activo.

---

# Autor

Desarrollado por:

```bash
Mayis2807
```

GitHub:

```bash
https://github.com/Mayis2807
```
