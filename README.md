# BlueERP — Bluebird B2B Fleet Management System

Repository ini sudah di-reset menjadi **Codex-ready checkpoint** untuk membangun Bluebird B2B Fleet / CRM / Finance MVP.

## Keputusan Stack Final untuk MVP

**Stack utama: Laravel 12 + Livewire 3 + MySQL + Tailwind + Spatie Permission.**

Alasan:
- Paling cepat untuk demo MVP karena sesuai master plan revisi.
- Cocok untuk CRUD kompleks, RBAC, dashboard internal, dan flow operasional.
- Livewire cukup untuk realtime ringan via polling/event; Supabase/Next bisa menjadi roadmap v2.
- Lebih aman untuk kebutuhan B2B enterprise internal dibanding rewrite penuh ke stack baru di awal.

## Modul MVP

1. Auth + RBAC
2. GM Dashboard
3. CRM / Client / Meeting Logs
4. Fleet Management + Vehicle State Machine
5. Pool Operation + Driver Assignment
6. Booking / Dispatch
7. Finance: PO → Invoice → Payment
8. E-Voucher
9. Maintenance
10. HR backend-only untuk Super Admin
11. Multi-level Drill Down
12. Demo Data Seeder 50+ records

## File penting

- `PROJECT_MASTERPLAN.md` — arah besar project dan fase kerja.
- `PROJECT_PRD.md` — requirement produk untuk engineer/Codex.
- `AGENTS.md` — instruksi agent parallel untuk Codex/OpenClaw/Antigravity.
- `CHECKPOINT_CURRENT.md` — status checkpoint terbaru.
- `docs/CODEX_MASTER_PROMPT.md` — prompt siap pakai untuk mulai coding di Codex.

## Cara kerja

1. Buka repo ini di Codex.
2. Minta Codex membaca `AGENTS.md`, `PROJECT_MASTERPLAN.md`, `PROJECT_PRD.md`, dan `CHECKPOINT_CURRENT.md`.
3. Jalankan dari **Phase 0** dulu.
4. Setiap selesai checkpoint, commit ke GitHub dengan format: `checkpoint: phase-X nama-checkpoint`.
5. Update `CHECKPOINT_CURRENT.md` setiap fase selesai.

## Prinsip penting

- Jangan loncat ke fitur besar sebelum foundation selesai.
- Jangan ubah stack tanpa ADR baru.
- Jangan tampilkan HR di dashboard utama.
- Fokus demo end-to-end ke Pak Kobi: booking, pool assign, invoice/payment, dashboard, drill-down, contingency driver.
