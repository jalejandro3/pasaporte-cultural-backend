# Pasaporte Cultural UNIR - Backend 🚀

API backend desarrollada en Laravel 10.10 para la gestión de actividades culturales, usuarios y sistema de autenticación con integración QR.

---

## Características Principales ✨

- **Autenticación JWT** con refresh token y recuperación de contraseña
- **Gestión de actividades culturales** con registro e inscripción mediante QR
- **Sistema de roles** (admin/usuario normal)
- **Geolocalización** de países y ciudades
- **Perfiles de usuario** personalizables
- **Endpoints administrativos** protegidos
- Integración con **Simple QR Code**
- Configuración mediante variables de entorno

---

## Requisitos Previos 📋

- PHP 8.4
- Composer 2.5+
- MySQL 8.0+
- Node.js 18+ (opcional para assets)
- Extensiones PHP: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

---

## Instalación ⚙️

1. Clonar repositorio:
```bash
git clone https://github.com/tu-usuario/pasaporte-cultural-backend.git
cd pasaporte-cultural-backend
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar variables de entorno:
```bash
cp .env.example .env
```

4. Configurar en .env:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pasaporte_cultural_backend
DB_USERNAME=root
DB_PASSWORD=root

JWT_SECRET=thisismysecretkey
QR_SECRET=thisismysecretkey
```
5. Ejecutar migraciones:
```bash
php artisan migrate --seed
```

6. Ejecutar seeders:
```bash
php artisan db:seed
```

7. Ejecutar servidor de desarrollo:
```bash
php artisan serve
```
## Endpoints API 🔌

### Autenticación 🔐
| Método | Endpoint | Descripción | Middleware |
|--------|----------|-------------|------------|
| POST   | `/api/auth/login` | Login de usuario | - |
| POST   | `/api/auth/register` | Registro de usuario | `validate.domain`, `validate.role` |
| POST   | `/api/auth/forgot-password` | Recuperación de contraseña | - |
| POST   | `/api/auth/reset-password` | Resetear contraseña | - |
| POST   | `/api/auth/refresh-token` | Refrescar token JWT | - |
| POST   | `/api/auth/validate-token` | Validar token JWT | - |

### Actividades Culturales 🎭
| Método | Endpoint | Descripción | Middleware |
|--------|----------|-------------|------------|
| GET    | `/api/activities` | Listar todas las actividades | `jwt` |
| GET    | `/api/activities/{id}` | Obtener detalles de una actividad | `jwt` |
| GET    | `/api/activities/enrolled` | Actividades en las que el usuario está inscrito | `jwt` |
| POST   | `/api/activities/register` | Registrar participación en actividad | `jwt`, `validate.qr` |
| POST   | `/api/activities` | Crear nueva actividad (admin) | `jwt`, `admin.user` |
| GET    | `/api/activities/autocomplete` | Búsqueda autocompletada (admin) | `jwt`, `admin.user` |
| GET    | `/api/activities/user` | Actividades por usuario (admin) | `jwt`, `admin.user` |
| GET    | `/api/activities/attendance` | Reporte de asistencia (admin) | `jwt`, `admin.user` |

### Usuarios 👤
| Método | Endpoint | Descripción | Middleware |
|--------|----------|-------------|------------|
| GET    | `/api/users/profile` | Perfil de usuario | `jwt` |
| PUT    | `/api/users/profile` | Actualizar perfil | `jwt`, `validate.domain` |
| GET    | `/api/users` | Listar todos usuarios (admin) | `jwt`, `admin.user` |
| PUT    | `/api/users/{id}` | Actualizar rol de usuario (admin) | `jwt`, `admin.user` |
| DELETE | `/api/users/{id}` | Eliminar usuario (admin) | `jwt`, `admin.user` |

### QR Codes 📲
| Método | Endpoint | Descripción | Middleware |
|--------|----------|-------------|------------|
| POST   | `/api/qr-code/regenerate` | Regenerar código QR | `jwt`, `admin.user` |

### Localizaciones 🌍
| Método | Endpoint | Descripción | Middleware |
|--------|----------|-------------|------------|
| GET    | `/api/countries` | Listar países | `jwt` |
| GET    | `/api/countries/{id}/cities` | Ciudades por país | `jwt` |

---

## Variables de Entorno Clave 🔑

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| `APP_NAME` | Nombre de la aplicación | `"Pasaporte Cultural UNIR"` |
| `APP_ENV` | Entorno de la aplicación | `local`, `production` |
| `APP_KEY` | Clave de cifrado de Laravel | Generada con `php artisan key:generate` |
| `APP_DEBUG` | Modo depuración | `true` (desarrollo), `false` (producción) |
| `APP_URL` | URL base de la aplicación | `http://localhost` |
| `DB_CONNECTION` | Tipo de conexión a la base de datos | `mysql` |
| `DB_HOST` | Host de la base de datos | `127.0.0.1` |
| `DB_PORT` | Puerto de la base de datos | `3306` |
| `DB_DATABASE` | Nombre de la base de datos | `pasaporte_cultural_backend` |
| `DB_USERNAME` | Usuario de la base de datos | `root` |
| `DB_PASSWORD` | Contraseña de la base de datos | `root` |
| `JWT_SECRET` | Secreto para tokens JWT | `thisismysecretkey` |
| `JWT_ISS` | Emisor de los tokens JWT | `http://localhost` |
| `JWT_ALGORITHM` | Algoritmo de cifrado JWT | `HS256` |
| `QR_SECRET` | Secreto para generación de códigos QR | `thisismysecretkey` |
| `MAIL_MAILER` | Driver para envío de correos | `smtp` |
| `MAIL_HOST` | Servidor SMTP | `127.0.0.1` |
| `MAIL_PORT` | Puerto SMTP | `1025` |
| `MAIL_FROM_ADDRESS` | Email de notificaciones | `pasaporte-cultural@unir.net` |
| `MAIL_FROM_NAME` | Nombre del remitente | `"Pasaporte Cultural UNIR"` |
| `VALID_DOMAINS` | Dominios permitidos para registro | `@unir.net,@comunidadunir.net` |
---

## Configuración Avanzada ⚙️

### Middlewares 🛡️
| Middleware | Descripción |
|------------|-------------|
| `jwt` | Verifica que el token JWT sea válido y esté activo. |
| `admin.user` | Restringe el acceso solo a usuarios con rol de administrador. |
| `validate.qr` | Valida que el código QR escaneado sea auténtico y esté asociado a una actividad válida. |
| `validate.domain` | Verifica que el dominio del correo electrónico esté permitido para el registro. |
| `validate.role` | Asegura que el rol asignado durante el registro sea válido. |

### Comandos de Artisan 🛠️
```bash
# Generar clave de aplicación
php artisan key:generate

# Generar nuevo secreto JWT
php artisan jwt:secret

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Limpiar caché de configuración
php artisan config:cache

# Limpiar caché de rutas
php artisan route:cache

# Limpiar caché de vistas
php artisan view:cache
```
