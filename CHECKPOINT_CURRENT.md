# CHECKPOINT_CURRENT.md

## Current Checkpoint

**Checkpoint ID:** PHASE-0.1-SCAFFOLD-COMPLETE  
**Date:** 2026-06-02  
**Status:** Completed  
**Branch:** main  
**Backup Branch:** backup/pre-blueerp-reset-20260602

## What Changed

- Laravel 12 foundation initialized in repository root.
- Livewire 3 installed and published (`config/livewire.php`, frontend assets).
- Tailwind build pipeline confirmed via Vite.
- Spatie Laravel Permission installed and migration/config published.
- `.env.example` hardened with safe local placeholders (no secrets).
- Base dashboard shell created with:
  - topbar
  - sidebar
  - content wrapper
  - role-aware sidebar placeholder
- Dashboard route now points to initial BlueERP dashboard page.
- User model updated with `HasRoles` trait.
- Seeder updated with baseline role set + super-admin seed user.

## Current Decision

Use **Laravel 12 + Livewire 3 + MySQL + Tailwind + Spatie Permission** for MVP demo.

## Validation Result

Executed successfully:

1. `composer install`
2. `npm install`
3. `php artisan key:generate`
4. `php artisan migrate:fresh --seed`
5. `npm run build`
6. `php artisan test`

## Next Task for Codex

Start **Checkpoint 0.2 — RBAC Foundation**:

1. Define permission matrix by module.
2. Add route middleware guards.
3. Implement role-based sidebar visibility (real role checks by access scope).
4. Seed demo users per role.
5. Commit: `checkpoint: phase-0 auth rbac foundation`

## Do Not Do Yet

- Do not jump to full business modules before RBAC checkpoint is validated.
- Do not migrate to Next.js/Supabase.
- Do not expose HR in main dashboard.
- Do not commit real credentials/secrets.
