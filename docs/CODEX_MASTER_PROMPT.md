# CODEX_MASTER_PROMPT.md

Gunakan prompt ini di Codex untuk mulai coding.

---

Kamu adalah senior Laravel architect + product engineer. Repo ini adalah project BlueERP / Bluebird B2B Fleet Management MVP.

Baca file berikut sebelum coding:

1. `README.md`
2. `PROJECT_MASTERPLAN.md`
3. `PROJECT_PRD.md`
4. `AGENTS.md`
5. `CHECKPOINT_CURRENT.md`
6. `docs/BLUEBIRD_ERP_MASTER_PLAN_REVISED.md`

## Keputusan Final

Gunakan stack:

- Laravel 12
- Livewire 3
- MySQL
- Tailwind
- Spatie Permission

Jangan pindah ke Next.js/Supabase. Itu hanya future roadmap.

## Tugas Sekarang

Kerjakan **Checkpoint 0.1 — Scaffold Laravel Foundation**.

### Deliverables

1. Initialize Laravel 12 project in current repo root.
2. Install and configure Livewire 3.
3. Install and configure Tailwind.
4. Install Spatie Laravel Permission.
5. Prepare `.env.example` with safe placeholders.
6. Create base app layout:
   - topbar
   - sidebar
   - content wrapper
   - role-aware menu placeholder
7. Create initial dashboard route.
8. Add README local setup instructions if needed.
9. Update `CHECKPOINT_CURRENT.md` with what was completed.

### Important Rules

- Do not delete these planning files.
- Do not commit secrets.
- Keep implementation incremental.
- Make migrations clean and repeatable.
- Commit with message:
  `checkpoint: phase-0 scaffold laravel foundation`

### Validation Commands

```bash
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan test
```

If Laravel scaffold cannot be completed in one pass, stop at the smallest working checkpoint and document blockers in `CHECKPOINT_CURRENT.md`.

---

After completing Checkpoint 0.1, continue to Checkpoint 0.2 only after the scaffold is validated.
