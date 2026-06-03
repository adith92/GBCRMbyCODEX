# Changelog

Semua perubahan penting di repo ini dicatat di file ini.

## [8.0] - 2026-06-03

- Mendorong BBCodex ke arah **production-grade backend + premium UX** tanpa mengubah stack utama.
- Menambahkan **Partner / Vendor module** lengkap dengan list, detail, create, dan edit flow.
- Menambahkan **Reports Dashboard** yang lebih nyata, menggantikan placeholder lama.
- Menambahkan **demo-only tools**:
  - role switcher
  - reset demo seed button untuk super-admin
- Menambahkan **Cmd+K style command palette** dan menyelaraskan shell layout menjadi command-center style.
- Merombak dashboard menjadi **hero command center** dengan persona per role, KPI compact, chart revenue visual, dan quick links yang lebih kaya.
- Menambahkan visual timeline untuk:
  - PO approval
  - dispatch audit trail
  - maintenance detail
- Menambahkan search support untuk partner/vendor.
- Menambahkan print-friendly rules pada UI detail pages.
- Menambahkan feature test baru untuk:
  - reports dashboard
  - vendor module
  - partner search scope
  - demo role switcher
- Validation final berhasil dengan hasil `96 passed`.

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
