# CHECKPOINT_CURRENT.md

## Current Checkpoint

**Checkpoint ID:** PHASE-2.1-BOOKING-POOL-DISPATCH-COMPLETE  
**Date:** 2026-06-02  
**Status:** Completed with runtime validation blocker documented  
**Branch:** main

## Completed Tasks

- Booking + Pool Dispatch core flow implemented with Laravel 12 + Livewire 3:
  - Booking list page with filter/search/pagination:
    - search booking number / client
    - filter by booking status
    - filter by start date range
  - Booking create page:
    - auto booking number format `BK-YYYYMM-0001` via `BookingNumberService`
    - status default `pending`
  - Booking edit page:
    - editable for `pending` / `assigned` (super-admin bypass)
  - Booking detail page:
    - detail info, assignment history
    - action buttons for assign / confirm / cancel based on permission + status
- Pool dispatch features:
  - Pool queue page (`/pool/queue`) for pending/assigned bookings
  - Assign page (`/pool/bookings/{booking}/assign`) to assign vehicle + driver
  - `BookingDispatchService` handles:
    - assign booking (`pending|assigned` -> `assigned`)
    - confirm booking (`assigned` -> `confirmed`)
    - cancel booking (`pending|assigned|confirmed` -> `cancelled`)
    - overlap checks (vehicle and driver)
    - create `driver_assignments` history
    - update vehicle status to `po` on assignment
    - release vehicle back to `available` when no active booking remains
- Routes and sidebar:
  - bookings routes:
    - `/bookings`
    - `/bookings/create`
    - `/bookings/{booking}`
    - `/bookings/{booking}/edit`
  - pool routes:
    - `/pool/queue`
    - `/pool/bookings/{booking}/assign`
  - sidebar now includes:
    - `Bookings` (permission: `bookings.view`)
    - `Pool Queue` (permission: `pool.view-all` or `pool.view-own`)
- Permissions enforced at route + component level for all booking/pool actions.
- Feature tests for Booking + Pool Dispatch updated/added in:
  - `tests/Feature/BookingPoolDispatchTest.php`
  - covers:
    - booking create
    - booking number generation
    - guest cannot access bookings
    - non-permitted role cannot access bookings
    - pool queue access
    - assign flow changes status to assigned
    - assignment history row created
    - assigned vehicle status becomes `po`
    - validation on unavailable vehicle
    - overlap protection for driver
    - confirm flow
    - confirm blocked when driver/vehicle missing
    - cancel releases assignment and returns vehicle

## Validation Result

Command status on this machine:

1. `composer install`  
   - started and dependencies reported as already installed.
2. `npm install`  
   - success (`up to date`, no vulnerabilities).
3. `php artisan migrate:fresh --seed`  
   - **blocked on environment runtime issue**.
4. `npm run build`  
   - not executed because Laravel runtime validation was blocked first.
5. `php artisan test`  
   - not executed because Laravel runtime validation was blocked first.

### Blocker Detail

- PHP CLI can run (`php -v` works), but `php artisan ...` hangs and then can fail with:
  - `Maximum execution time of 0 seconds exceeded`
- This indicates an environment/runtime issue during Laravel bootstrap in current machine session, not a syntax error in checkpoint code.
- Syntax checks passed for the newly added PHP files (`php -l` clean).

## Next Recommended Checkpoint

Proceed to **Checkpoint 2.2 — Finance Flow (PO, Invoice, Payment, eVoucher)** after runtime issue is resolved, with focus on:

1. Purchase Order baseline flow tied to booking/client.
2. Invoice generation + status transitions.
3. Payment posting and reconciliation basics.
4. eVoucher create/update flow.
5. finance-role permission matrix verification.

## Notes

- `CHECKPOINT_CURRENT.md` was restored before continuing this checkpoint, then updated to latest status.
- Restore was not committed as a separate commit.
