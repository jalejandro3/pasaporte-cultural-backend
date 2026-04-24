# Pasaporte Cultural UNIR — Backend

Backend application for the Pasaporte Cultural system at Universidad Internacional de La Rioja (UNIR). This system verifies and records student participation in cultural activities to grant university credits.

## About This Branch

This branch (`refactor-tdd-paa`) is a complete rewrite of the original codebase found in the `main` branch. The original implementation followed a traditional layered approach (use cases, service layer, thin controllers, repositories). This rewrite rebuilds the application from scratch using TDD and Hexagonal Architecture, with the goal of learning and applying these practices deliberately.

## Purpose

UNIR offers cultural activities (reading clubs, cinema forums, theater workshops, concerts) that allow students to earn up to six recognized credits throughout their university career. This system serves as the verification and registration mechanism: it confirms that a student actually attended an activity, invested the required time, and therefore qualifies for credits.

## Architecture

Built using **Hexagonal Architecture (Ports & Adapters)** with **Test-Driven Development (TDD)**.

### Layers

- **Domain** — Entities, value objects, enums, exceptions, and repository interfaces (ports). Contains all business rules. Depends on nothing external.
- **Application** — Use cases that orchestrate domain logic through ports. No business rules live here, only workflow coordination.
- **Infrastructure** — Adapters (Eloquent repositories, controllers, external services). Depends on Application and Domain.

### Project Structure

```
app/
├── Application/
│   ├── Activity/
│   │   ├── ShowActivity.php
│   │   └── ActivityDTO.php
│   ├── Participation/
│   │   ├── CreateParticipation.php
│   │   └── FinishParticipation.php
│   └── User/
│       ├── CreateAssistant.php
│       ├── UserDTO.php
│       ├── InvalidEmailDomainException.php
│       └── UserExistsException.php
├── Domain/
│   ├── Activity/
│   │   ├── Activity.php
│   │   └── ActivityRepository.php
│   ├── Participation/
│   │   ├── Participation.php
│   │   ├── ParticipationStatus.php
│   │   ├── ParticipationRepository.php
│   │   ├── FinishedParticipationException.php
│   │   ├── NotFoundParticipationException.php
│   │   ├── ParticipationExistsException.php
│   │   ├── ParticipationVerificationCodeMismatchException.php
│   │   └── PriorEndDateParticipationException.php
│   └── User/
│       ├── User.php
│       ├── UserRole.php
│       ├── UserRepository.php
│       └── InvalidEmailFormatException.php
tests/
├── ObjectMother/
│   ├── ActivityMother.php
│   ├── AdminMother.php
│   └── AssistantMother.php
└── Unit/
    ├── Application/
    │   ├── Activity/
    │   │   └── ShowActivityTest.php
    │   ├── Participation/
    │   │   ├── CreateParticipationTest.php
    │   │   └── FinishParticipationTest.php
    │   └── User/
    │       └── CreateAssistantTest.php
    └── Domain/
        ├── ActivityTest.php
        ├── ParticipationTest.php
        └── UserTest.php
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

## Tech Stack

- **PHP 8.4** with **Laravel**
- **PHPUnit 12** for testing
- **Ramsey UUID** for domain-generated identifiers

## Running Tests

```bash
php artisan test
```

Current test suite: **22 tests, 37 assertions**.

## Patterns & Practices

- **TDD (Red-Green-Refactor)** — Every feature starts with a failing test.
- **Hexagonal Architecture** — Domain is isolated from infrastructure.
- **Domain-specific exceptions** — Each business rule violation has its own exception class.
- **Object Mother** — Test factories (`ActivityMother`, `AssistantMother`, `AdminMother`) reduce test setup noise.
- **Immutable DTOs** — Data Transfer Objects with `final readonly` and `fromEntity()` factory methods.
- **Stub vs Mock** — Stubs control return values; Mocks verify method calls.
- **Guard clauses** — Business rule violations are caught early in use cases and domain methods.
- **Enums for closed sets** — `ParticipationStatus` and `UserRole` as PHP enums with type safety.
