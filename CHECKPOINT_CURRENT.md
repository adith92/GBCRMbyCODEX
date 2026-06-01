# CHECKPOINT_CURRENT.md

## Current Checkpoint

**Checkpoint ID:** PHASE-0.2-AUTH-RBAC-COMPLETE  
**Date:** 2026-06-02  
**Status:** Completed  
**Branch:** main  
**Backup Branch:** backup/pre-blueerp-reset-20260602

## Completed Tasks

- Authentication foundation completed with Laravel Breeze (Blade):
  - Login route and screen active
  - Logout active
  - Guest users redirected to login
  - Authenticated users redirected to dashboard
  - Dashboard protected by `auth` + `permission:dashboard.view`
- RBAC baseline finalized using Spatie Permission:
  - 8 baseline roles created
  - Full baseline permission matrix created per module
  - Role-to-permission mapping implemented (including wildcard expansion like `clients.*`)
- Seeder foundation completed:
  - `RbacSeeder` created for roles, permissions, and permission mapping
  - Demo users seeded exactly as requested with password `password`
- Route/middleware foundation completed:
  - Permission/role middleware aliases registered in `bootstrap/app.php`
  - Placeholder protected routes added:
    - `/dashboard`
    - `/crm`
    - `/fleet`
    - `/drivers`
    - `/pool`
    - `/bookings`
    - `/finance`
    - `/maintenance`
    - `/reports`
    - `/admin/hr`
  - HR route group locked to `role:super-admin`
- Sidebar updated to permission-aware visibility:
  - Finance menu hidden from pool users unless permission exists
  - Pool menu hidden from finance users unless permission exists
  - HR menu shown only when both `admin.access` and `hr.view` are present
- Auth + RBAC tests added and passing:
  - guest cannot access dashboard
  - authenticated user can access dashboard
  - super-admin has all permissions
  - pool-staff has no finance permissions
  - finance has no `pool.assign-driver`
  - non super-admin cannot access HR route

## Validation Result

Executed successfully:

1. `composer install`
2. `npm install`
3. `php artisan migrate:fresh --seed`
4. `npm run build`
5. `php artisan test`

All tests passing.

## Next Checkpoint Recommendation

Proceed to **Checkpoint 1.1 — Fleet + Driver Core**:

1. Vehicles migration/model/controller foundation
2. Drivers migration/model/controller foundation
3. Vehicle status/state rules (`available`, `po`, `maintenance`, `hold`)
4. Basic Fleet/Driver CRUD shell with permission guards
5. Commit target: `checkpoint: phase-1 fleet driver core`

## Do Not Do Yet

- Do not rewrite stack to Next.js/Supabase.
- Do not expose HR in main dashboard navigation for non-super-admin.
- Do not implement full business workflows before Fleet/Driver core baseline is stable.
- Do not commit production secrets.
