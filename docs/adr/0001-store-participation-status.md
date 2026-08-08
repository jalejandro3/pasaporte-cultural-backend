# ADR-0001: Store participation status in the database

## Status
Accepted

## Context
The `Participation` entity derives its status (`IN_PROCESS`, `COMPLETED`,
`NOT_COMPLETED`) from `startTime`, `endTime`, and the activity's required
hours, via the `status()` method. Status is not part of the entity's stored
state; it is computed on the fly.

However, the system needs to query participations by status: an administrator
or the university will, for example, want to retrieve all completed
participations. With status computed in memory, answering that query would
require loading every participation and filtering them one by one in the
application, which does not scale.

## Considered Options
1. **Compute status on the fly (do not persist it).** Status is always
   correct by construction, but any query by status requires scanning all
   rows and filtering in memory.
2. **Store status as a column.** Enables querying by status with a single
   indexed query, at the cost of keeping a derived value in sync with its
   source.

## Decision
Store status as a column in the `participations` table, to allow efficient
querying by status through a single indexed query.

## Consequences

### Positive
- Queries by status resolve with a single query (`WHERE status = ?`), without
  loading and filtering in memory.
- A foreseen query pattern (administrative reporting) is efficient and
  scalable.

### Negative
- Status now exists in two places: computed by the entity and stored in the
  table. This introduces a risk of desynchronization.
- The integrity of the stored status depends on **every** mutation going
  through the domain (the `finish()` method), which updates `endTime` and
  `status` in the same operation.
- The only path to desynchronization is a write that bypasses the domain
  (manual UPDATE, data migration script, another application on the same
  database). This case is out of the application's scope and is mitigated
  by the governance rule: **nothing mutates status except the domain.**
- Future needs for bulk status changes (e.g. an administrator finalizing N
  participations) must be implemented as a use case that iterates over the
  domain, not as a direct bulk UPDATE to the database.
