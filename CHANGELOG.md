# Changelog

Semua perubahan penting di repo ini dicatat di file ini.

## [7.3] - 2026-06-03

- Menambahkan `docs/FINAL_DEMO_REVIEW_PACK.md` sebagai ringkasan handoff final.
- Menyelaraskan README, checkpoint, dan summary docs ke status final phase 7.
- Menutup roadmap praktis demo-ready + deploy-ready untuk versi saat ini.
- Menyiapkan GitHub Actions CI dasar dan menstabilkan metadata `package.json` agar `package-lock.json` tidak drift karena nama folder clone lokal.

## [7.2] - 2026-06-03

- Railway project di-link ulang dari clone workspace sehat.
- `APP_URL` production diselaraskan ke domain aktif `https://gbdemo01.up.railway.app`.
- Smoke test live berhasil untuk login, dashboard, CRM, fleet, drivers, bookings, pool, finance, maintenance, dan HR super-admin access.
- Verifikasi `403` untuk HR route sebagai finance user berhasil.

## [7.1] - 2026-06-03

- Menambahkan extended search filters berbasis scope untuk clients, vehicles, drivers, bookings, invoices, dan maintenance.
- Menambahkan activity timeline filter per tipe aktivitas beserta summary card ringan.
- Menambahkan test coverage baru untuk scoped search dan activity filtering.

## [6.4] - 2026-06-03

- Menambahkan dashboard quick access ke Workspace Search dan Recent Activity.
- Menyelesaikan navigation hardening phase 6.

## [6.3] - 2026-06-03

- Menambahkan permission-aware global search.
- Menambahkan recent activity timeline lintas workflow.
