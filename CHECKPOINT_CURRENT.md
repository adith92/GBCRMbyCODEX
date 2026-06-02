# CHECKPOINT_CURRENT.md

## Current Checkpoint

**Checkpoint ID:** PHASE-5.2-RAILWAY-DEPLOYED  
**Date:** 2026-06-02  
**Status:** Completed  
**Branch:** main

## Deployment Result

- Railway project: `GBCRMbyCODEX`
- Railway environment: `production`
- Railway public URL: `https://web-production-c81b8.up.railway.app`
- App service: `web`
- Database service: `MySQL`

## What Was Fixed

- Aligned Railway runtime to PHP 8.4 so it matches the current lockfile and Symfony package requirements.
- Removed the problematic `LOG_STDERR_FORMATTER` deployment env usage that caused Monolog redeclare crashes.
- Stabilized Railway startup flow so it uses:
  - `php artisan migrate --force`
  - `php artisan db:seed --class=RbacSeeder --force`
  - demo seeding only when `ENABLE_DEMO_SEED=true` and the `clients` table is still empty
- Fixed migration bootstrap ordering issues that broke clean database initialization:
  - `client_contacts`
  - `bookings`
  - `driver_assignments`
- Added deferred foreign-key migration so fresh database setup works deterministically on Railway and on manual demo resets.
- Performed a one-time manual demo database reset using the public Railway MySQL proxy so the demo environment could be initialized cleanly.

## Railway Startup Rules

Permanent startup behavior now follows these rules:

- normal deploys use `php artisan migrate --force`
- RBAC baseline can be refreshed safely on deploy
- demo seed only runs when explicitly enabled and when demo tables are still empty
- `migrate:fresh` is not used in permanent Railway startup

## Smoke Test Result

Verified against the live Railway URL:

- `GET /login` -> `200`
- `superadmin@blueerp.test / password` login -> redirect to `/dashboard`
- `GET /dashboard` as super-admin -> `200`
- `GET /crm/clients` as super-admin -> `200`
- `GET /fleet/vehicles` as super-admin -> `200`
- `GET /drivers` as super-admin -> `200`
- `GET /bookings` as super-admin -> `200`
- `GET /pool/queue` as super-admin -> `200`
- `GET /finance` as super-admin -> `200`
- `GET /maintenance` as super-admin -> `200`
- `GET /admin/hr/drivers` as super-admin -> `200`
- `GET /admin/hr/drivers` as finance user -> `403`

## Deployment Notes

- Demo accounts are available because RBAC and demo seed data are now present in the Railway database.
- `APP_URL` is set to the generated Railway domain.
- Deployment finished successfully after runtime, logging, and migration-order stabilization.

## Known TODO

- Run a broader end-to-end browser pass over booking, finance, maintenance, and logout flow on the live deployment.
- Optionally add a safer first-class deploy config for Railway pre-deploy/start settings if future infra automation needs to be fully codified.
- Rename local folder to `GBCRMbyCODEX` after this deployment checkpoint is fully recorded.

## Next Recommended Checkpoint

Proceed to **Post-Deployment QA / UI Polish Follow-up** or **Folder Rename + Repo Hygiene**.
