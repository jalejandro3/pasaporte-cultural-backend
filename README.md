[![CI](https://github.com/jalejandro3/pasaporte-cultural-backend/actions/workflows/ci.yml/badge.svg)](https://github.com/jalejandro3/pasaporte-cultural-backend/actions/workflows/ci.yml)

# Pasaporte Cultural UNIR — Backend

Backend application for the Pasaporte Cultural system at Universidad Internacional de La Rioja (UNIR). This system verifies and records student participation in cultural activities to grant university credits.

## Purpose

UNIR offers cultural activities (reading clubs, cinema forums, theater workshops, concerts) that allow students to earn up to six recognized credits throughout their university career. This system serves as the verification and registration mechanism: it confirms that a student actually attended an activity, invested the required time, and therefore qualifies for credits.

## Architecture

Built using **Hexagonal Architecture (Ports & Adapters)** with **Test-Driven Development (TDD)**.

### Layers

- **Domain** — Entities, value objects, enums, exceptions, and repository interfaces (ports). Contains all business rules. Depends on nothing external.
- **Application** — Use cases that orchestrate domain logic through ports. No business rules live here, only workflow coordination.
- **Infrastructure** — Adapters (Eloquent repositories, controllers, external services). Depends on Application and Domain.

## Architecture Decisions

Significant technical decisions are recorded as ADRs (Architecture Decision Records) in [`docs/adr/`](docs/adr/), each capturing the context, the options considered, and the trade-offs accepted.

## Project Structure

```
app/
├── Application/
│ ├── Activity/
│ │ ├── ActivityDTO.php
│ │ └── ShowActivity.php
│ ├── Participation/
│ │ ├── CreateParticipation.php
│ │ ├── FinishParticipation.php
│ │ ├── NotFoundParticipationException.php
│ │ ├── ParticipationExistsException.php
│ │ └── ParticipationVerificationCodeMismatchException.php
│ └── User/
│ ├── AssignmentRoleException.php
│ ├── CannotDemoteLastAdminException.php
│ ├── ChangeUserRole.php
│ ├── CreateAssistant.php
│ ├── InvalidEmailDomainException.php
│ ├── NonExistentUserException.php
│ ├── UserDTO.php
│ └── UserExistsException.php
├── Domain/
│ ├── Activity/
│ │ ├── Activity.php
│ │ └── ActivityRepository.php
│ ├── Participation/
│ │ ├── FinishedParticipationException.php
│ │ ├── Participation.php
│ │ ├── ParticipationRepository.php
│ │ ├── ParticipationStatus.php
│ │ └── PriorEndDateParticipationException.php
│ └── User/
│ ├── InvalidEmailFormatException.php
│ ├── User.php
│ ├── UserRepository.php
│ └── UserRole.php
├── Infrastructure/
│ └── Participation/
│ ├── EloquentParticipation.php
│ └── EloquentParticipationRepository.php
├── Http/
│ └── Controllers/
│ └── Controller.php
├── Models/
│ └── User.php
└── Providers/
└── AppServiceProvider.php

tests/
├── Unit/
│ ├── Application/
│ │ ├── Activity/
│ │ │ └── ShowActivityTest.php
│ │ ├── Participation/
│ │ │ ├── CreateParticipationTest.php
│ │ │ └── FinishParticipationTest.php
│ │ └── User/
│ │ ├── ChangeUserRoleTest.php
│ │ └── CreateAssistantTest.php
│ └── Domain/
│ ├── ActivityTest.php
│ ├── ParticipationTest.php
│ └── UserTest.php
├── Feature/ # empty — first feature test lands with the CreateParticipation slice
├── ObjectMother/
│ ├── ActivityMother.php
│ ├── AdminMother.php
│ └── AssistantMother.php
└── TestCase.php
```

### Domain Organization

Code is organized by **business concept**, not by technical type. Everything related to Participation (entity, enum, exceptions, repository interface) lives under `Domain/Participation/`.

## Domain Concepts

### User

Represents anyone who interacts with the system. Has a role (`assistant` or `admin`) that determines permissions. Generates its own UUID on creation. Validates email format at construction time.

### Activity

A cultural event offered by UNIR. Has a title, location (country, city, address), required hours for completion, and a verification code (used for QR generation). The verification code can be regenerated, which invalidates the previous one.

### Participation

The core concept of the system. Represents a student's attendance at an activity. Has three states: `in_process` (student scanned QR to start), `completed` (student met required hours), and `not_completed` (student did not meet required hours). Once finalized, a participation cannot be finalized again.

## Business Rules

### Participation
- A participation is created when a student scans the activity's QR code for the first time.
- A participation is finalized when the student scans the QR code a second time.
- The system calculates elapsed hours and determines if the participation is completed or not completed.
- A finalized participation (completed or not completed) cannot be finalized again.
- End time cannot be before start time.
- The scanned verification code must match the activity's current verification code.
- A student cannot have more than one participation per activity.

### Activity
- Every activity generates a unique verification code on creation.
- The verification code can be regenerated, invalidating the previous one.
- Only administrators can see the verification code. Assistants receive it as null.

### User Registration
- Only assistant users can be registered through the system.
- Email must be a valid format (domain validation).
- Email must be from the UNIR domain (`unir.net`).
- Email and identity document must be unique across users.

### User Role Management
- Only admin users can change a user's role.
- An admin cannot be demoted if they are the last remaining admin in the system.
- Changing the role of a non-existent user raises an exception.
- Assigning a user their current role is a no-op (returns early, no change, no error).
  Intentionally untested: if removed, the only consequence is a redundant UPDATE, not a business failure.

## Roadmap

### Current: Vertical slice — CreateParticipation (end-to-end)

Taking `CreateParticipation` from domain to HTTP as the first complete
hexagonal slice: executable, demonstrable, and fixing the core cracks it
touches along the way.

Order (respecting physical + TDD dependencies):

1. ✅ Domain — fix Participation cracks (exception layer convention)
2. ✅ Port — align `ParticipationRepository` to the fixed domain
3. ✅ Migration — `participations` table
4. ✅ Eloquent model
5. ✅ Feature test (hits real DB — guides the adapter in red)
6. ✅ Adapter — `EloquentParticipationRepository` (implement stub methods, currently throwing "not implemented")
7. ⬜ Route
8. ⬜ Controller

**Next action:** piece 5 — feature test hitting the real DB (own session; needs test DB + MariaDB service in CI).

### Backlog

#### Done
- ✅ CI pipeline (GitHub Actions): run tests on push and PR — build status badge
- ✅ Update `actions/checkout` to `@v7` (resolved Node 20 deprecation warning)
- ✅ PHPStan + Larastan static analysis (level 5), baselined and running in CI
- ✅ Remove `password` from `Domain\User\User` (authentication concern, not domain — surfaced by PHPStan)

#### CI / tooling (pending)
- ⬜ Add MariaDB service to CI (needed once feature tests hit the DB — slice piece 5)
- ⬜ Coverage reporting (Codecov): enable coverage, publish badge, track over time — account already created

#### PHPStan baseline (tracked debt — resolve, don't grow)
- ⬜ `User::$firstName / $lastName / $idDocument` never read → resolved by ShowProfile use case
- ⬜ `Participation::$assistant` never read → resolved by a use case that reads the participation's assistant
- ⬜ `EloquentParticipationRepository` stub methods throw "not implemented" → resolved in slice piece 6 (adapter)
- ⬜ Raise PHPStan level gradually (5 → 6 → 7…) as code allows, clearing baseline entries

#### Core audit findings (not blocking the slice)
- ⬜ `ShowActivity` does not handle activity-not-found (same null bug fixed in ChangeUserRole)
- ⬜ Participation use cases don't validate the actor (unlike ChangeUserRole)
- ⬜ `country` / `city` / `address` are plain strings, not value objects
- ⬜ "Credits" mentioned in domain but no Credit concept exists
- ⬜ `users` migration desynced from the `User` entity (missing `role`, `id_document`, `first_name`/`last_name`)

#### Deferred FKs (participations)
- ⬜ Add FKs `participations.assistant_id` → `users` and `participations.activity_id` → `activities` (deferred until both target tables exist and are aligned)
- ⬜ Decide `onDelete` policy for those FKs with credit-history retention in mind (restrict to preserve history vs. cascade)

## Tech Stack

- **PHP 8.4** with **Laravel**
- **PHPUnit 12** for testing
- **PHPStan 2 + Larastan** for static analysis (level 5)
- **Ramsey UUID** for domain-generated identifiers

## Running Tests

```bash
php artisan test
```

Current test suite: **26 tests, 45 assertions**.

## Static Analysis

```bash
composer analyse
```

PHPStan (level 5) with the Larastan extension, run in CI on every push. Pre-existing findings are baselined and tracked in the backlog as pending work — the baseline should only shrink, never grow.

## Patterns & Practices

- **TDD (Red-Green-Refactor)** — Every feature starts with a failing test.
- **Hexagonal Architecture** — Domain is isolated from infrastructure.
- **Static analysis** — PHPStan level 5 with Larastan, enforced in CI.
- **Domain-specific exceptions** — Each business rule violation has its own exception class.
- **Object Mother** — Test factories (`ActivityMother`, `AssistantMother`, `AdminMother`) reduce test setup noise.
- **Immutable DTOs** — Data Transfer Objects with `final readonly` and `fromEntity()` factory methods.
- **Stub vs Mock** — Stubs control return values; Mocks verify method calls.
- **Guard clauses** — Business rule violations are caught early in use cases and domain methods.
- **Enums for closed sets** — `ParticipationStatus` and `UserRole` as PHP enums with type safety.
