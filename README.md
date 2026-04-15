# Pasaporte Cultural UNIR — Backend

REST API backend for the Pasaporte Cultural platform, developed as the final project for the MSc in Software Engineering at Universidad Internacional de La Rioja (UNIR), Spain.

The platform allows university students to browse cultural activities, register attendance via QR code scanning, and track completion status toward academic credit. Administrators manage activities, users, and participation reports.

---

## Architecture

The original version follows a **layered architecture** — a deliberate decision based on the scope and complexity of the domain. With a well-defined but relatively contained business model, a layered approach provided clear separation between presentation, application, and data layers without the overhead of a more complex architectural style.

The backend is fully decoupled from the frontend, exposing a REST API consumed by a React application.

**Currently being refactored** to apply **Hexagonal Architecture** and **TDD** practices:
- Domain logic is being extracted into a pure PHP layer with no framework dependencies
- Repository interfaces are defined in the domain and implemented in infrastructure using Eloquent
- All domain behavior is covered by unit tests running without framework bootstrap

This refactor is a deliberate exercise — revisiting past architectural decisions with new knowledge and improving them with justification.

---

## Tech Stack

- PHP 8.4 / Laravel 10
- MySQL 8.0
- JWT Authentication (with refresh token)
- Simple QR Code for activity validation
- REST API consumed by a decoupled React frontend

---

## Key Features

- **Role-based access control** — attendee and administrator roles with distinct permissions
- **QR-based attendance flow** — scan to start, scan again to finish; completion determined by time invested vs. required hours
- **Activity management** — administrators create and manage activities with regenerable QR codes
- **Participation tracking** — status per user: in progress, completed, not completed
- **Admin reporting** — query participation by user or by activity
- **Domain validation** — email registration restricted to allowed institutional domains

---

## API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | User login |
| POST | `/api/auth/register` | User registration |
| POST | `/api/auth/forgot-password` | Password recovery |
| POST | `/api/auth/reset-password` | Password reset |
| POST | `/api/auth/refresh-token` | Refresh JWT token |
| POST | `/api/auth/validate-token` | Validate JWT token |

### Cultural Activities
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/activities` | List all activities |
| GET | `/api/activities/{id}` | Get activity details |
| GET | `/api/activities/enrolled` | Activities the user is enrolled in |
| POST | `/api/activities/register` | Register participation via QR |
| POST | `/api/activities` | Create activity (admin) |
| GET | `/api/activities/autocomplete` | Autocomplete search (admin) |
| GET | `/api/activities/user` | Activities by user (admin) |
| GET | `/api/activities/attendance` | Attendance report (admin) |

### Users
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users/profile` | Get user profile |
| PUT | `/api/users/profile` | Update profile |
| GET | `/api/users` | List all users (admin) |
| PUT | `/api/users/{id}` | Update user role (admin) |
| DELETE | `/api/users/{id}` | Delete user (admin) |

### QR Codes
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/qr-code/regenerate` | Regenerate QR code (admin) |

### Locations
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/countries` | List countries |
| GET | `/api/countries/{id}/cities` | Cities by country |

---

## Local Setup

**Requirements:** PHP 8.4, Composer 2.5+, MySQL 8.0+

```bash
git clone https://github.com/jalejandro3/pasaporte-cultural-backend.git
cd pasaporte-cultural-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

Key `.env` variables:
```env
DB_DATABASE=pasaporte_cultural_backend
JWT_SECRET=your_jwt_secret
QR_SECRET=your_qr_secret
VALID_DOMAINS=@unir.net,@comunidadunir.net
```

---

## Learning Branches

This repository also serves as a technical learning journal:

| Branch | Focus |
|--------|-------|
| `main` | Original Laravel implementation |
| `learning/http-fundamentals` | HTTP from scratch — no framework |
| `learning/php-no-framework` | PHP router and request handling without Laravel |
| `learning/tdd-hex-arch-ddd` | TDD, Hexagonal Architecture, and DDD applied |

---

## Frontend

The React frontend is maintained in a separate repository:
[pasaporte-cultural-frontend](https://bitbucket.org/pasaporte-cultural-unir/pasaporte-cultural-frontend)
