# AGENTS.md — Parallel Coding Instructions for Codex

## Operating Mode

Kerjakan project ini secara checkpoint. Jangan membuat semua sekaligus tanpa validasi. Setiap agent wajib membaca `PROJECT_MASTERPLAN.md`, `PROJECT_PRD.md`, `CHECKPOINT_CURRENT.md`, mengambil task sesuai role, implementasi kecil tapi lengkap, menjalankan test/build, commit, lalu update checkpoint.

## Stack Lock

MVP stack dikunci:

- Laravel 12
- Livewire 3
- MySQL
- Tailwind
- Spatie Permission

Jangan rewrite ke Next.js/Supabase kecuali ada ADR baru.

## Agent Assignment

| Agent | Fokus | Output |
|---|---|---|
| Agent 1 | Database migrations + models | migrations, relationships, factories |
| Agent 2 | Auth + RBAC + seeders | roles, permissions, middleware |
| Agent 3 | Fleet module | vehicle CRUD, state machine |
| Agent 4 | CRM module | clients, contacts, meeting logs |
| Agent 5 | Booking + Dispatch + Pool | booking flow, pool order queue |
| Agent 6 | Finance | PO, invoice, payment, e-voucher |
| Agent 7 | Drill-down engine | breadcrumb, navigation stack |
| Agent 8 | Maintenance + HR backend | maintenance logs, admin/hr routes |
| Agent 9 | GM Dashboard + Reporting | KPI cards, report snapshots |
| Agent 10 | Demo Data + QA + Polish | DemoDataSeeder, responsive, demo script |

## Checkpoints

### Checkpoint 0.1 — Scaffold

- Create Laravel 12 app.
- Configure `.env.example`.
- Install Livewire, Tailwind, Spatie.
- Create base layout.
- Commit: `checkpoint: phase-0 scaffold laravel foundation`

### Checkpoint 0.2 — RBAC

- Roles and permissions.
- Route middleware.
- Sidebar role visibility.
- Seed demo users.
- Commit: `checkpoint: phase-0 auth rbac foundation`

### Checkpoint 1.1 — Fleet + Driver

- Vehicles CRUD.
- Drivers CRUD.
- Vehicle states.
- Commit: `checkpoint: phase-1 fleet driver core`

### Checkpoint 1.2 — CRM

- Client CRUD.
- Contacts.
- Meeting Logs.
- Commit: `checkpoint: phase-1 crm meeting logs`

### Checkpoint 2.1 — Booking + Pool

- Booking create.
- Pool queue.
- Assign driver/vehicle.
- Commit: `checkpoint: phase-2 booking pool dispatch`

### Checkpoint 2.2 — Finance

- PO, invoice, payments.
- E-voucher.
- Commit: `checkpoint: phase-2 finance flow`

### Checkpoint 3.1 — Drill-down + Dashboard

- Breadcrumb stack.
- GM KPI dashboard.
- Entity links.
- Commit: `checkpoint: phase-3 dashboard drilldown`

### Checkpoint 4.1 — Demo Ready

- DemoDataSeeder.
- QA script.
- Responsive polish.
- Commit: `checkpoint: phase-4 demo ready`

## Coding Standards

- Use Laravel conventions.
- Keep controllers thin.
- Prefer Livewire components for interactive CRUD.
- Use FormRequest or Livewire validation.
- Use policies/permissions for sensitive actions.
- Keep enums/constants for status transitions.
- Do not expose HR menu except Super Admin.
- No secrets committed.
