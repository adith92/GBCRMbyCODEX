# CHECKPOINT_CURRENT.md

## Current Checkpoint

**Checkpoint ID:** RESET-001  
**Date:** 2026-06-02  
**Status:** Repository reset to BlueERP planning pack  
**Branch:** main  
**Backup Branch:** backup/pre-blueerp-reset-20260602  

## What Changed

- Old AI Studio app content replaced with BlueERP planning checkpoint.
- MVP stack decision locked to Laravel 12 + Livewire 3 + MySQL + Spatie.
- Added masterplan, PRD, agent instructions, and Codex prompt.
- Ready for Codex to start Phase 0 scaffold.

## Current Decision

Use **Laravel 12 + Livewire 3 + MySQL + Tailwind + Spatie Permission** for MVP demo.

Reason: fastest, lowest-risk path for enterprise dashboard + RBAC + CRUD-heavy workflow.

## Next Task for Codex

Start **Checkpoint 0.1 — Scaffold Laravel Foundation**:

1. Initialize Laravel 12 project in repository root.
2. Install Livewire 3.
3. Setup Tailwind.
4. Install Spatie Laravel Permission.
5. Create `.env.example`.
6. Create base layout, dashboard shell, and role-aware sidebar placeholder.
7. Commit: `checkpoint: phase-0 scaffold laravel foundation`

## Do Not Do Yet

- Do not implement all modules before scaffold validation.
- Do not migrate to Next.js/Supabase.
- Do not expose HR in main dashboard.
- Do not create fake production credentials.

## Validation Required

```bash
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan test
```
