[![CI](https://github.com/jalejandro3/pasaporte-cultural-backend/actions/workflows/ci.yml/badge.svg)](https://github.com/jalejandro3/pasaporte-cultural-backend/actions/workflows/ci.yml)
[![codecov](https://codecov.io/github/jalejandro3/pasaporte-cultural-backend/graph/badge.svg?token=JP7IEQ8RX5)](https://codecov.io/github/jalejandro3/pasaporte-cultural-backend)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4)
![PHPStan](https://img.shields.io/badge/PHPStan-level%205-2EBB4E)

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
│   ├── Activity/
│   │   ├── ActivityDTO.php
│   │   └── ShowActivity.php
│   ├── Participation/
│   │   ├── CreateParticipation.php
│   │   ├── FinishParticipation.php
│   │   ├── NotFoundParticipationException.php
│   │   ├── ParticipationExistsException.php
│   │   └── ParticipationVerificationCodeMismatchException.php
│   └── User/
│       ├── AssignmentRoleException.php
│       ├── CannotDemoteLastAdminException.php
│       ├── ChangeUserRole.php
│       ├── CreateAssistant.php
│       ├── InvalidEmailDomainException.php
│       ├── NonExistentUserException.php
│       ├── UserDTO.php
│       └── UserExistsException.php
├── Domain/
│   ├── Activity/
│   │   ├── Activity.php
│   │   └── ActivityRepository.php
│   ├── Participation/
│   │   ├── FinishedParticipationException.php
│   │   ├── InvalidUuidException.php
│   │   ├── NonPositiveHoursException.php
│   │   ├── Participation.php
│   │   ├── ParticipationId.php
│   │   ├── ParticipationRepository.php
│   │   ├── ParticipationStatus.php
│   │   ├── PriorEndDateParticipationException.php
│   │   └── RequiredHours.php
│   └── User/
│       ├── InvalidEmailFormatException.php
│       ├── User.php
│       ├── UserRepository.php
│       └── UserRole.php
├── Infrastructure/
│   ├── Activity/
│   │   ├── EloquentActivity.php
│   │   └── EloquentActivityRepository.php
│   ├── Participation/
│   │   ├── EloquentParticipation.php
│   │   └── EloquentParticipationRepository.php
│   └── User/
│       ├── EloquentUser.php
│       └── EloquentUserRepository.php
├── Http/
│   └── Controllers/
│       └── Controller.php
└── Providers/
    └── AppServiceProvider.php

tests/
├── Unit/
│   ├── Application/
│   │   ├── Activity/
│   │   │   └── ShowActivityTest.php
│   │   ├── Participation/
│   │   │   ├── CreateParticipationTest.php
│   │   │   └── FinishParticipationTest.php
│   │   └── User/
│   │       ├── ChangeUserRoleTest.php
│   │       └── CreateAssistantTest.php
│   └── Domain/
│       ├── Activity/
│       │   └── ActivityTest.php
│       ├── Participation/
│       │   ├── ParticipationIdTest.php
│       │   ├── ParticipationTest.php
│       │   └── RequiredHoursTest.php
│       └── User/
│           └── UserTest.php
├── Feature/
│   ├── EloquentActivityRepositoryTest.php
│   ├── EloquentParticipationRepositoryTest.php
│   └── EloquentUserRepositoryTest.php
├── ObjectMother/
│   ├── ActivityMother.php
│   ├── AdminMother.php
│   └── AssistantMother.php
└── TestCase.php
```

### Domain Organization

Code is organized by **business concept**, not by technical type. Everything related to Participation (entity, identity value object, required-hours value object, enum, exceptions, repository interface) lives under `Domain/Participation/`. The test suite mirrors this structure: `tests/Unit/Domain/` is split by subdomain (`Activity/`, `Participation/`, `User/`) to match `app/`.

## Domain Concepts

All three aggregates (User, Activity, Participation) generate their own UUID identifiers in the domain, independent of the database. Entities that are reconstructed from persistence expose a `fromDatabase()` named constructor, kept separate from `create()` so that rebuilding a stored entity never regenerates its identity.

Aggregates reference other aggregates **by identity, not by holding full objects** (Vernon's rule from *Effective Aggregate Design*). A Participation references its assistant and activity by id, not by embedding the full `User`/`Activity` objects.

### User

Represents anyone who interacts with the system. Has a role (`assistant` or `admin`) that determines permissions. Generates its own UUID on creation. Validates email format at construction time (in `create()` only; `fromDatabase()` trusts already-persisted data).

### Activity

A cultural event offered by UNIR. Generates its own UUID on creation via `create()`; reconstructed from storage via `fromDatabase()`. Has a title, location (country, city, address), required hours for completion, and a verification code (used for QR generation). The verification code can be regenerated, which invalidates the previous one.

### Participation

The core concept of the system. Represents a student's attendance at an activity. It is an aggregate with its own identity (`ParticipationId`) and references its assistant and activity **by id** (`assistantId`, `activityId`), not by holding full objects.

At creation it captures a `RequiredHours` value object — a snapshot of the activity's required hours at that moment — so the participation is judged against the hours that applied when it was created, even if the activity later changes. This snapshot is persisted with the participation, which makes reconstruction self-contained: rebuilding a Participation reads a single row and never needs to load the User or Activity aggregates.

Has three states: `in_process` (student scanned QR to start), `completed` (student met required hours), and `not_completed` (student did not meet required hours). The status is derived: `in_process` when there is no end time; otherwise `RequiredHours::isSatisfiedBy()` decides between `completed` and `not_completed`. Once finalized, a participation cannot be finalized again.

### Value Objects

- **`ParticipationId`** — Participation's aggregate identity. Immutable, private constructor, `generate()` for new ids (UUID v4) and `fromString()` for reconstruction (validates the UUID, throwing `InvalidUuidException` on malformed input). `value()` exposes the raw string; `equals()` compares by value.
- **`RequiredHours`** — Immutable (`readonly`) snapshot of an activity's required hours held by a Participation. Guards against non-positive hours (`NonPositiveHoursException`). Its `isSatisfiedBy(float $hours)` method encapsulates the completion check, so the entity delegates the "did the participation meet the hours?" decision to the value object rather than computing it inline.

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

Taking `CreateParticipation` from HTTP to DB. Building the HTTP layer revealed
that the use case depends on persisting and reconstructing Activity and User
(not just Participation) — so the slice now includes their persistence.

**Participation persistence**
1. ✅ Domain — fix Participation cracks
2. ✅ Port — align `ParticipationRepository`
3. ✅ Migration — `participations` table (UUID primary key + `required_hours`)
4. ✅ Eloquent model
5. ✅ Feature test — `save` against real DB
6. ✅ Adapter `save` + container binding
7. ✅ Refactor Participation to reference User/Activity by identity — `ParticipationId` (own aggregate identity) + `RequiredHours` (snapshot value object); `status()` delegates to `RequiredHours::isSatisfiedBy`
8. ✅ Adapter `findByActivityIdAndAssistantId` — reconstructs from a single row via `fromDatabase` (no User/Activity lookup needed); covered for in-process, completed, not-completed, and not-found

**Activity persistence** (required by the use case)
9. ✅ Migration — `activities` table (UUID primary key)
10. ✅ Domain — `create()` / `fromDatabase()` named constructors (separate creation from reconstitution)
11. ✅ Eloquent model (`EloquentActivity`)
12. ✅ Adapter `EloquentActivityRepository` (`findById` reconstructs via `fromDatabase`)
13. ✅ Feature test (verifies full reconstitution) + container binding

**User persistence** (required by the use case)
14. ✅ Migration — reconcile `users` table with domain (UUID PK, first/last name, id_document, role as string; keep auth columns; split `sessions` and `password_reset_tokens` into own migrations)
15. ✅ `EloquentUser` model — moved from `app/Models` to `Infrastructure/User` (UUID key, domain fillable, `'hashed'` cast, `Notifiable`); `config/auth.php` repointed
16. ✅ Domain — `create()` / `fromDatabase()` named constructors on `User` (email validation in `create()` only)
17. ✅ Adapter `EloquentUserRepository` — `save(User, password)` (hashed via cast, password kept out of domain) + `update(User)` (domain fields only, password untouched); getters added to `User` (cleared PHPStan baseline)
18. ✅ Feature tests (`save` asserts password hashed, `update` asserts password untouched); `ChangeUserRole` switched from `save` to `update`
19. ✅ Container binding `UserRepository` → `EloquentUserRepository`

**HTTP layer** (once persistence is complete)
20. ⬜ Route `POST /api/participations`
21. ⬜ Controller + `CreateParticipationRequest`
22. ⬜ End-to-end feature test (HTTP → DB)

**Next action:** the HTTP layer (piece 20). Persistence for all three aggregates (Participation, Activity, User) is complete; the only thing left to close the CreateParticipation vertical slice end-to-end is exposing it over HTTP — the first time the project serves anything through an HTTP endpoint.

### Backlog

#### Done
- ✅ CI pipeline (GitHub Actions): run tests on push and PR — build status badge
- ✅ Update `actions/checkout` to `@v7` (resolved Node 20 deprecation warning)
- ✅ PHPStan + Larastan static analysis (level 5), running in CI
- ✅ Remove `password` from `Domain\User\User` (authentication concern, not domain — surfaced by PHPStan)
- ✅ ADR-0001: store participation status in the database
- ✅ Add MariaDB service to CI (feature tests now run against a real DB in the pipeline)
- ✅ Unify Activity identity to UUID (removed int/UUID identifier inconsistency; all aggregates now domain-generated UUIDs)
- ✅ Activity `create()` / `fromDatabase()` named constructors (private constructor; reconstitution never regenerates identity)
- ✅ Activity persistence complete (`EloquentActivityRepository.findById` + feature test + container binding)
- ✅ Code coverage reporting via Codecov (95%, PCOV + Clover, uploaded from CI on every push)
- ✅ Enums as strings (`ParticipationStatus`, `UserRole`): domain is single source of truth; DB stores strings, not native enums
- ✅ User persistence complete — migration reconciled, `EloquentUser` in Infrastructure, `create()`/`fromDatabase()` named constructors, adapter with `save` (password) + `update` (no password), `ChangeUserRole` uses `update`
- ✅ Separate `save`/`update` on `UserRepository` — registration (with password) vs. domain update (without); surfaced by ChangeUserRole misusing `save` for a role change
- ✅ Clear PHPStan baseline — `getFirstName`/`getLastName`/`getIdDocument` added to `User` (baseline now empty; level 5 passes with no baseline)
- ✅ Refactor Participation to an aggregate that references User/Activity by identity (Vernon) — introduced `ParticipationId` and `RequiredHours` value objects; `status()` delegates the hours check to `RequiredHours`
- ✅ `RequiredHours` value object — immutable, `isSatisfiedBy()` completion check, non-positive guard
- ✅ `ParticipationId` value object — identity with `generate()`/`fromString()`, UUID validation, `equals()`
- ✅ Participation persistence complete — migration with UUID PK + `required_hours`, adapter `save` persists id and snapshot, `findByActivityIdAndAssistantId` reconstructs from a single row (in-process / completed / not-completed / not-found all covered)
- ✅ Organize `tests/Unit/Domain/` by subdomain to mirror `app/`

#### PHPStan baseline (now empty — keep it that way)
- ✅ `User::$firstName / $lastName / $idDocument` never read → resolved (getters added for the adapter; baseline cleared)
- ⬜ Raise PHPStan level gradually (5 → 6 → 7…) as code allows

#### Domain typing refinements
- ⬜ Model `activityId` / `assistantId` on Participation as `ActivityId` / `AssistantId` value objects (currently plain strings — reference-by-identity works, this is a typing refinement)
- ⬜ Add `equals()` to `RequiredHours` if instances ever need to be compared
- ⬜ `country` / `city` / `address` on Activity are plain strings, not value objects

#### Test maintenance
- ⬜ The four `findByActivityIdAndAssistantId` feature tests share setup (create activity + assistant, participation, save, find, assert common fields). Extract a helper or data provider if they grow.

#### CI configuration debt
- ⬜ CI DB connection relies on an empty password against a root-password MariaDB — works via container behavior but is fragile; make credentials explicit and consistent across `.env.example`, `phpunit.xml`, and the CI service
- ⬜ Node 20 deprecation warning from `codecov/codecov-action` (internal dependency of the action; resolves when Codecov updates — not actionable now)

#### Wiring not directly tested
- ⬜ Container bindings (`ParticipationRepository` / `ActivityRepository` / `UserRepository` → their Eloquent adapters) have no dedicated tests; covered indirectly by feature tests and, once built, the end-to-end HTTP test

#### Core audit findings (not blocking the slice)
- ✅ `users` table reconciled with the `User` entity (was missing `role`, `id_document`, `first_name`/`last_name`) — done in User persistence
- ⬜ `ShowActivity` does not handle activity-not-found (same null bug fixed in ChangeUserRole)
- ⬜ Participation use cases don't validate the actor (unlike ChangeUserRole)
- ⬜ "Credits" mentioned in domain but no Credit concept exists

#### Deferred FKs (participations)
- ⬜ Add FKs `participations.assistant_id` → `users` and `participations.activity_id` → `activities` (deferred until both target tables exist and are aligned)
- ⬜ Decide `onDelete` policy for those FKs with credit-history retention in mind (restrict to preserve history vs. cascade)

#### Auth debt
- ⬜ `assistant_id` will come from the request body temporarily; once auth exists, it must come from the authenticated user (impersonation risk until then)
- ⬜ ADR pending: document passing `password` through `UserRepository::save(User, password)` while keeping it out of the domain entity (context, options, decision, consequences)

#### Auth infrastructure debt (Laravel tables kept as-is for now)
- ⬜ `sessions` table: `user_id` is `foreignId` (BIGINT), doesn't match `users.id` (UUID). Harmless while sessions aren't used in DB (stateless token API). Revisit with the session-driver decision during auth phase.
- ⬜ Session driver is `database` (default from Laravel). For a stateless token API, sessions in DB are likely unnecessary. Evaluate switching driver (cookie/array) and dropping the `sessions` table when implementing auth.
- ⬜ `password_reset_tokens` table kept from Laravel OOTB. Confirm whether password-reset-by-email is in scope; drop if not.
- ⬜ `MustVerifyEmail` on `EloquentUser` (email verification flow) — auth phase, alongside notifications, guards, and session-driver decisions.
- ⬜ `password` persisted via the `EloquentUser` model's `'hashed'` cast; domain never handles it — enters through use case and repository as an explicit auth concern (see pending ADR).

## Tech Stack

- **PHP 8.4** with **Laravel**
- **PHPUnit 12** for testing
- **PHPStan 2 + Larastan** for static analysis (level 5)
- **Codecov** for coverage reporting (PCOV + Clover)
- **Ramsey UUID** for domain-generated identifiers

## Running Tests

```bash
php artisan test
```

## Static Analysis

```bash
composer analyse
```

PHPStan (level 5) with the Larastan extension, run in CI on every push. The baseline is currently empty — level 5 passes with no suppressed findings. Keep it that way: the baseline should only shrink, never grow.

## Patterns & Practices

- **TDD (Red-Green-Refactor)** — Every feature starts with a failing test.
- **Hexagonal Architecture** — Domain is isolated from infrastructure.
- **Aggregates & reference by identity** — Aggregates reference each other by id, not by holding full objects (Vernon, *Effective Aggregate Design*). Keeps aggregates small and reconstruction self-contained.
- **Value Objects** — Immutable, equality by value, invariants enforced at construction (an instance cannot exist in an invalid state). Identity value objects (`ParticipationId`) wrap and validate UUIDs; "smart" value objects (`RequiredHours`) carry behavior, not just data.
- **Architecture Decision Records** — Significant decisions documented in `docs/adr/`.
- **Named constructors** — `create()`/`generate()` for new instances (generates identity), `fromDatabase()`/`fromString()` for reconstitution (receives identity, does not revalidate persisted data). Constructor kept private.
- **Static analysis** — PHPStan level 5 with Larastan, enforced in CI, empty baseline.
- **Coverage tracking** — line coverage reported to Codecov on every push (PCOV + Clover).
- **Domain-specific exceptions** — Each business rule violation has its own exception class.
- **Object Mother** — Test factories (`ActivityMother`, `AssistantMother`, `AdminMother`) reduce test setup noise.
- **Immutable DTOs** — Data Transfer Objects with `final readonly` and `fromEntity()` factory methods.
- **Stub vs Mock** — Stubs control return values; Mocks verify method calls.
- **Guard clauses** — Business rule violations are caught early in use cases and domain methods.
- **Enums for closed sets** — `ParticipationStatus` and `UserRole` as PHP enums with type safety.
