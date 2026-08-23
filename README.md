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
- **Infrastructure** — Adapters (Eloquent repositories, HTTP controllers, requests, resources, exception rendering). Depends on Application and Domain.

## Architecture Decisions

Significant technical decisions are recorded as ADRs (Architecture Decision Records) in [`docs/adr/`](docs/adr/), each capturing the context, the options considered, and the trade-offs accepted.

## Project Structure

```
app/
├── Application/
│   ├── Activity/
│   │   ├── ActivityDTO.php
│   │   ├── NotFoundActivityException.php
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
│       ├── NotFoundUserException.php
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
│   ├── Controllers/
│   │   ├── Controller.php
│   │   └── ParticipationController.php
│   ├── Exceptions/
│   │   ├── ExceptionRenderer.php
│   │   └── ProblemDetail.php
│   ├── Requests/
│   │   └── CreateParticipationRequest.php
│   └── Resources/
│       └── ParticipationResource.php
└── Providers/
    └── AppServiceProvider.php

routes/
├── api.php
├── console.php
└── web.php

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
│   ├── Domain/
│   │   ├── Activity/
│   │   │   └── ActivityTest.php
│   │   ├── Participation/
│   │   │   ├── ParticipationIdTest.php
│   │   │   ├── ParticipationTest.php
│   │   │   └── RequiredHoursTest.php
│   │   └── User/
│   │       └── UserTest.php
│   └── Http/
│       └── Exceptions/
│           ├── ExceptionRendererTest.php
│           └── ProblemDetailTest.php
├── Feature/
│   ├── Http/
│   │   └── ParticipationControllerTest.php
│   └── Infrastructure/
│       ├── Activity/
│       │   └── EloquentActivityRepositoryTest.php
│       ├── Participation/
│       │   └── EloquentParticipationRepositoryTest.php
│       └── User/
│           └── EloquentUserRepositoryTest.php
├── ObjectMother/
│   ├── ActivityMother.php
│   ├── AdminMother.php
│   └── AssistantMother.php
└── TestCase.php
```

### Code Organization

Domain, Application, and Infrastructure code is organized by **business concept**, not by technical type. Everything related to Participation (entity, identity value object, required-hours value object, enum, exceptions, repository interface) lives under `Participation/`. The test suite mirrors this: `tests/Unit/Domain/` and `tests/Unit/Application/` are split by subdomain (`Activity/`, `Participation/`, `User/`) to match `app/`, while `tests/Feature/` is split by boundary — `Http/` for tests that exercise the HTTP stack end-to-end, and `Infrastructure/{subdomain}/` for repository adapter tests against a real database.

## Domain Concepts

All three aggregates (User, Activity, Participation) generate their own UUID identifiers in the domain, independent of the database. Entities that are reconstructed from persistence expose a `fromDatabase()` named constructor, kept separate from `create()` so that rebuilding a stored entity never regenerates its identity.

Aggregates reference other aggregates **by identity, not by holding full objects** (Vernon's rule from *Effective Aggregate Design*). A Participation references its assistant and activity by id, not by embedding the full `User`/`Activity` objects.

### User

Represents anyone who interacts with the system. Has a role (`assistant` or `admin`) that determines permissions. Generates its own UUID on creation. Validates email format at construction time (in `create()` only; `fromDatabase()` trusts already-persisted data). Reconstruction by id is available through `UserRepository::findById`, with the string role converted back to the `UserRole` enum in the adapter (strictly, via `UserRole::from`, so a corrupt stored role fails loudly).

### Activity

A cultural event offered by UNIR. Generates its own UUID on creation via `create()`; reconstructed from storage via `fromDatabase()`. Has a title, location (country, city, address), required hours for completion, and a verification code (used for QR generation). The verification code can be regenerated, which invalidates the previous one.

### Participation

The core concept of the system. Represents a student's attendance at an activity. It is an aggregate with its own identity (`ParticipationId`) and references its assistant and activity **by id** (`assistantId`, `activityId`), not by holding full objects.

At creation it captures a `RequiredHours` value object — a snapshot of the activity's required hours at that moment — so the participation is judged against the hours that applied when it was created, even if the activity later changes. This snapshot is persisted with the participation, which makes reconstruction self-contained: rebuilding a Participation reads a single row and never needs to load the User or Activity aggregates.

Has three states: `in_process` (student scanned QR to start), `completed` (student met required hours), and `not_completed` (student did not meet required hours). The status is derived: `in_process` when there is no end time; otherwise `RequiredHours::isSatisfiedBy()` decides between `completed` and `not_completed`. Once finalized, a participation cannot be finalized again.

### Value Objects

- **`ParticipationId`** — Participation's aggregate identity. Immutable, private constructor, `generate()` for new ids (UUID v4) and `fromString()` for reconstruction (validates the UUID, throwing `InvalidUuidException` on malformed input). `value()` exposes the raw string; `equals()` compares by value.
- **`RequiredHours`** — Immutable (`readonly`) snapshot of an activity's required hours held by a Participation. Guards against non-positive hours (`NonPositiveHoursException`). Its `isSatisfiedBy(float $hours)` method encapsulates the completion check, so the entity delegates the "did the participation meet the hours?" decision to the value object rather than computing it inline.
- **`ProblemDetail`** — Immutable (`readonly`) HTTP-layer value object holding the stable RFC 9457 fields for an error type (`type`, `title`, `status`). Its `toResponseBody(string $detail)` assembles the full Problem Details body, receiving the occurrence-specific `detail` at render time so the stable value objects can live in a shared map without mutable state.

## HTTP API

### `POST /api/participations`

Creates a participation (a student checking in to an activity). The request body carries `activity_id`, `assistant_id`, and `verification_code` (all `required|string`); `start_time` is generated server-side at check-in, not accepted from the client. The controller is thin: it validates through `CreateParticipationRequest`, invokes the `CreateParticipation` use case with ids, and returns a `ParticipationResource` wrapped in `data` with `201 Created`. Reconstruction of the activity and assistant, and all business validation, happen inside the use case.

`assistant_id` is read from the body **temporarily**, until authentication exists — see the auth debt in the backlog.

### Error responses — RFC 9457 Problem Details

Domain and application exceptions are translated to [RFC 9457 Problem Details](https://www.rfc-editor.org/rfc/rfc9457) responses (`application/problem+json`) by a centralized `ExceptionRenderer`, wired into `bootstrap/app.php` via `withExceptions`. The renderer holds a map of exception FQCN → `ProblemDetail` (stable `type`, `title`, `status`); the occurrence-specific `detail` comes from the exception message at render time. An unmapped exception returns `null` from the renderer, so Laravel falls through to its default handling (500).

The knowledge of "which HTTP status belongs to which exception" lives in the HTTP layer (the map), not in the domain/application exceptions — those stay ignorant of HTTP. Adding a new mapped error is a single new entry in the map.

| Exception | Status | `type` slug |
| --- | --- | --- |
| `NotFoundActivityException` | 404 | `activity-not-found` |
| `NotFoundUserException` | 404 | `user-not-found` |
| `ParticipationExistsException` | 409 | `participation-already-exists` |
| `ParticipationVerificationCodeMismatchException` | 422 | `verification-code-mismatch` |

The `type` is a stable identifier URI (it does not need to resolve). The `title` is stable per error type; the `detail` carries the occurrence message. `422` is used for the verification-code mismatch because the request is well-formed but semantically unprocessable, matching the RFC's 400-vs-422 distinction.

## Business Rules

### Participation
- A participation is created when a student scans the activity's QR code for the first time.
- A participation is finalized when the student scans the QR code a second time.
- The system calculates elapsed hours and determines if the participation is completed or not completed.
- A finalized participation (completed or not completed) cannot be finalized again.
- End time cannot be before start time.
- The scanned verification code must match the activity's current verification code (required, not nullable).
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

### Completed: Vertical slice — CreateParticipation (end-to-end)

`CreateParticipation` runs end-to-end from HTTP to DB. Building the HTTP layer
revealed that the use case depends on persisting and reconstructing Activity and
User (not just Participation) — so the slice grew to include their persistence,
the id-based refactor of the use case, and RFC 9457 error handling.

**Participation persistence**
- ✅ Domain — fix Participation cracks
- ✅ Port — align `ParticipationRepository`
- ✅ Migration — `participations` table (UUID primary key + `required_hours`)
- ✅ Eloquent model
- ✅ Feature test — `save` against real DB
- ✅ Adapter `save` + container binding
- ✅ Refactor Participation to reference User/Activity by identity — `ParticipationId` + `RequiredHours`; `status()` delegates to `RequiredHours::isSatisfiedBy`
- ✅ Adapter `findByActivityIdAndAssistantId` — reconstructs from a single row via `fromDatabase`; covered for in-process, completed, not-completed, not-found

**Activity persistence** (required by the use case)
- ✅ Migration — `activities` table (UUID primary key)
- ✅ Domain — `create()` / `fromDatabase()` named constructors
- ✅ Eloquent model (`EloquentActivity`)
- ✅ Adapter `EloquentActivityRepository` (`findById` reconstructs via `fromDatabase`)
- ✅ Feature test + container binding

**User persistence** (required by the use case)
- ✅ Migration — reconcile `users` table with domain (UUID PK, first/last name, id_document, role as string)
- ✅ `EloquentUser` model moved to `Infrastructure/User` (UUID key, domain fillable, `'hashed'` cast)
- ✅ Domain — `create()` / `fromDatabase()` named constructors on `User`
- ✅ Adapter `save(User, password)` + `update(User)`; getters added to `User`
- ✅ Feature tests; `ChangeUserRole` switched from `save` to `update`
- ✅ Container binding `UserRepository` → `EloquentUserRepository`
- ✅ `UserRepository::findById` — reconstruct assistant by id (string role → `UserRole` via strict `from`); covered for found and not-found

**Use case — receive ids and reconstruct**
- ✅ Rename `NonExistentUserException` → `NotFoundUserException` (naming consistency)
- ✅ Refactor `CreateParticipation` to receive ids and reconstruct Activity/User via repositories; `NotFoundActivityException` / `NotFoundUserException` on missing references; five use-case paths covered

**HTTP layer**
- ✅ Route `POST /api/participations` (`routes/api.php` enabled and registered)
- ✅ `CreateParticipationRequest` (validates `activity_id` / `assistant_id` / `verification_code` as required strings; `start_time` server-side)
- ✅ `ParticipationController` (thin, constructor injection, no try/catch — exceptions go to the handler) + `ParticipationResource` (snake_case, wraps in `data`)
- ✅ End-to-end happy-path feature test (201 + persisted participation, status and required_hours asserted)

**Error handling — RFC 9457 Problem Details**
- ✅ `ProblemDetail` value object (stable `type`/`title`/`status`, `toResponseBody(detail)`)
- ✅ `ExceptionRenderer` — FQCN → `ProblemDetail` map, `render(Throwable): ?JsonResponse`, `application/problem+json`; unit-tested including the unmapped (null) case
- ✅ Wire the renderer into `withExceptions` (bootstrap)
- ✅ Feature tests for all four error paths (404 activity, 404 user, 422 verification code, 409 duplicate) — full body asserted

**Next action:** the slice is closed. Candidate next slices: `FinishParticipation` over HTTP (second endpoint, reuses most of this machinery), the pending password ADR, or starting the authentication phase (which retires the `assistant_id`-in-body debt).

### Backlog

#### Done
- ✅ CI pipeline (GitHub Actions): run tests on push and PR — build status badge
- ✅ Update `actions/checkout` to `@v7` (resolved Node 20 deprecation warning)
- ✅ PHPStan + Larastan static analysis (level 5), running in CI
- ✅ Remove `password` from `Domain\User\User` (authentication concern, not domain — surfaced by PHPStan)
- ✅ ADR-0001: store participation status in the database
- ✅ Add MariaDB service to CI (feature tests run against a real DB in the pipeline)
- ✅ Unify Activity identity to UUID (all aggregates now domain-generated UUIDs)
- ✅ Activity `create()` / `fromDatabase()` named constructors
- ✅ Activity persistence complete (`EloquentActivityRepository.findById` + feature test + container binding)
- ✅ Code coverage reporting via Codecov (PCOV + Clover, uploaded from CI on every push)
- ✅ Enums as strings (`ParticipationStatus`, `UserRole`): domain is single source of truth; DB stores strings
- ✅ User persistence complete — migration reconciled, `EloquentUser` in Infrastructure, named constructors, adapter with `save` (password) + `update` (no password)
- ✅ Separate `save`/`update` on `UserRepository` — registration (with password) vs. domain update (without)
- ✅ `UserRepository::findById` — reconstruct by id, string role → `UserRole` via strict `from` in the adapter
- ✅ Clear PHPStan baseline — getters added to `User` (baseline now empty; level 5 passes with no baseline)
- ✅ Refactor Participation to reference User/Activity by identity (Vernon) — `ParticipationId` and `RequiredHours` value objects
- ✅ `RequiredHours` value object — immutable, `isSatisfiedBy()` completion check, non-positive guard
- ✅ `ParticipationId` value object — identity with `generate()`/`fromString()`, UUID validation, `equals()`
- ✅ Participation persistence complete — UUID PK + `required_hours`, `findByActivityIdAndAssistantId` (four paths covered)
- ✅ Rename `NonExistentUserException` → `NotFoundUserException` (naming consistency)
- ✅ Refactor `CreateParticipation` to receive ids and reconstruct Activity/User via repositories
- ✅ HTTP layer for CreateParticipation — route, `CreateParticipationRequest`, `ParticipationController`, `ParticipationResource`, end-to-end happy-path test
- ✅ RFC 9457 Problem Details error handling — `ProblemDetail`, `ExceptionRenderer` (FQCN map), wired into `withExceptions`, all four error paths covered end-to-end
- ✅ Organize tests by subdomain (`Unit/Domain`, `Unit/Application`) and by boundary (`Feature/Http`, `Feature/Infrastructure`)

#### Adopted conventions
- **RFC 9457 Problem Details** for all HTTP error responses (`application/problem+json`), with `type`/`title`/`status`/`detail`. New API — adopting the current standard rather than an ad-hoc error format.

#### PHPStan baseline (now empty — keep it that way)
- ⬜ Raise PHPStan level gradually (5 → 6 → 7…) as code allows

#### Domain typing refinements
- ⬜ Model `activityId` / `assistantId` on Participation as `ActivityId` / `AssistantId` value objects (currently plain strings — reference-by-identity works, this is a typing refinement)
- ⬜ Add `equals()` to `RequiredHours` if instances ever need to be compared
- ⬜ `country` / `city` / `address` on Activity are plain strings, not value objects

#### Application refinements
- ⬜ `UserRepository::existsById(string): bool` — `CreateParticipation` reconstructs the full assistant only to check it exists; a cheaper existence check avoids loading the whole aggregate

#### HTTP / error-handling refinements
- ⬜ RFC 9457 `instance` field — add a per-request correlation id, included in both the response and structured logs, for tracing (needs structured logging first)
- ⬜ Log unmapped exceptions (the ones that fall through the renderer to a 500) so unexpected errors are observable
- ⬜ Clock abstraction (PSR-20) for `start_time` — currently `new DateTimeImmutable()` directly in the controller, which is non-deterministic in tests; an injectable clock would make it testable
- ⬜ `save`/creation for Activity — no `ActivityRepository::save` yet (no use case creates activities). Feature tests seed activities via Eloquent directly. Add when the "admin creates activity" use case exists (its own slice).

#### Test maintenance
- ⬜ The `findByActivityIdAndAssistantId` and HTTP error feature tests share setup (seed activity/assistant, POST, assert body). Extract a helper or data provider if they grow.
- ⬜ Seeding is inconsistent across feature tests (Eloquent `create` directly vs. the `save` on the repository). Pick one convention.

#### CI configuration debt
- ⬜ CI DB connection relies on an empty password against a root-password MariaDB — fragile; make credentials explicit across `.env.example`, `phpunit.xml`, and the CI service
- ⬜ Node 20 deprecation warning from `codecov/codecov-action` (resolves when Codecov updates — not actionable now)
- ⬜ Add `.atl/` and `.codegraph/` (local tooling folders) to `.gitignore`

#### Wiring not directly tested
- ⬜ Container bindings (repositories → adapters) have no dedicated tests; covered indirectly by feature tests and the end-to-end HTTP tests

#### Core audit findings (not blocking)
- ⬜ `ShowActivity` does not handle activity-not-found (same null bug fixed in ChangeUserRole)
- ⬜ Participation use cases don't validate the actor (unlike ChangeUserRole)
- ⬜ "Credits" mentioned in domain but no Credit concept exists

#### Deferred FKs (participations)
- ⬜ Add FKs `participations.assistant_id` → `users` and `participations.activity_id` → `activities` (deferred until both target tables exist and are aligned)
- ⬜ Decide `onDelete` policy with credit-history retention in mind (restrict to preserve history vs. cascade)

#### Auth debt
- ⬜ `assistant_id` comes from the request body temporarily; once auth exists, it must come from the authenticated user (impersonation risk until then). `CreateParticipationRequest::authorize()` is a `true` placeholder for the same reason.
- ⬜ ADR pending: document passing `password` through `UserRepository::save(User, password)` while keeping it out of the domain entity (context, options, decision, consequences)

#### Auth infrastructure debt (Laravel tables kept as-is for now)
- ⬜ `sessions` table: `user_id` is `foreignId` (BIGINT), doesn't match `users.id` (UUID). Harmless while sessions aren't used in DB. Revisit with the session-driver decision during auth.
- ⬜ Session driver is `database` (Laravel default). For a stateless token API, evaluate switching driver and dropping the `sessions` table.
- ⬜ `password_reset_tokens` table kept from Laravel OOTB. Confirm whether password-reset-by-email is in scope; drop if not.
- ⬜ `MustVerifyEmail` on `EloquentUser` (email verification flow) — auth phase.
- ⬜ `password` persisted via the `EloquentUser` model's `'hashed'` cast; domain never handles it (see pending ADR).

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

- **TDD (Red-Green-Refactor)** — Every feature starts with a failing test. The weight of TDD sits where the logic lives (domain and use cases); thin adapters like controllers are covered by end-to-end feature tests rather than driven test-first line by line.
- **Hexagonal Architecture** — Domain is isolated from infrastructure. HTTP concerns (status codes, Problem Details, serialization) live in the HTTP layer; the domain never learns about HTTP.
- **Aggregates & reference by identity** — Aggregates reference each other by id, not by holding full objects (Vernon, *Effective Aggregate Design*). Keeps aggregates small and reconstruction self-contained.
- **Value Objects** — Immutable, equality by value, invariants enforced at construction. Identity value objects (`ParticipationId`) wrap and validate UUIDs; "smart" value objects (`RequiredHours`) carry behavior; HTTP value objects (`ProblemDetail`) hold the stable shape of an error response.
- **RFC 9457 Problem Details** — Standard error response format (`application/problem+json`). A centralized `ExceptionRenderer` maps exceptions to Problem Details by FQCN; the exception→status knowledge lives in the HTTP layer, not in the exceptions.
- **Named constructors** — `create()`/`generate()` for new instances (generates identity), `fromDatabase()`/`fromString()` for reconstitution (receives identity, does not revalidate). Constructor kept private.
- **Adapters translate primitives to domain types** — Repositories convert DB primitives to rich domain types on the way out (string → `UserRole` via `from`, Carbon → `DateTimeImmutable`, string → value objects) and back on the way in.
- **Architecture Decision Records** — Significant decisions documented in `docs/adr/`.
- **Static analysis** — PHPStan level 5 with Larastan, enforced in CI, empty baseline.
- **Coverage tracking** — line coverage reported to Codecov on every push (PCOV + Clover).
- **Domain-specific exceptions** — Each business rule violation has its own exception class.
- **Object Mother** — Test factories (`ActivityMother`, `AssistantMother`, `AdminMother`) reduce test setup noise.
- **Immutable DTOs** — Data Transfer Objects with `final readonly` and `fromEntity()` factory methods.
- **Stub vs Mock** — Stubs control return values; Mocks verify method calls.
- **Guard clauses** — Business rule violations are caught early in use cases and domain methods.
- **Enums for closed sets** — `ParticipationStatus` and `UserRole` as PHP enums with type safety.
