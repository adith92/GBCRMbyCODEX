# PROJECT_MASTERPLAN.md — BlueERP / Bluebird B2B Fleet Management

## 1. Tujuan Project

Membangun MVP demo enterprise untuk Bluebird B2B Fleet Management yang menggabungkan CRM client corporate, fleet availability, pool operation, driver assignment, booking/dispatch, PO, invoice, payment, e-voucher, GM dashboard, multi-level drill-down, dan HR backend-only.

Target utama: **demo yang terlihat matang, nyambung secara bisnis, dan bisa dipresentasikan ke stakeholder.**

## 2. Keputusan Arsitektur

### Stack MVP Final

- Backend: Laravel 12
- Frontend: Blade + Livewire 3
- Database: MySQL
- CSS/UI: Tailwind
- Auth/RBAC: Laravel Sanctum + Spatie Permission
- Deploy awal: Private server / shared hosting / VPS
- Future v2: Next.js + Supabase atau hybrid API

### Kenapa bukan Next.js + Supabase dulu?

Karena project ini butuh demo cepat, banyak CRUD/RBAC, dan flow internal enterprise. Next/Supabase bagus untuk v2, tapi untuk MVP demo sekarang akan memperbesar risiko rewrite.

## 3. Phase Map

### Phase 0 — Setup & Foundation

- Laravel 12 project scaffold
- MySQL env
- Auth
- Spatie Permission
- Base layout
- Sidebar per role
- Core migrations
- Base seeders

### Phase 1 — Core Modules

- Fleet CRUD
- Vehicle state machine
- Driver CRUD basic
- Client CRUD
- Meeting logs
- Role dashboards shell

### Phase 2 — Business Flow

- Booking / Dispatch
- Pool receive order
- Assign driver + vehicle
- PO generation
- Invoice generation
- Payment tracking
- E-voucher
- Breadcrumb + drill-down base

### Phase 3 — Operations & Reporting

- Maintenance flow
- Driver contingency flow
- HR backend-only
- GM KPI dashboard
- Report snapshots
- Export-ready tables

### Phase 4 — Demo Polish

- DemoDataSeeder 50+ records
- End-to-end test scenarios
- Mobile responsive 375px
- UI polish
- Demo script Pak Kobi

## 4. Core Entity Map

- users
- roles / permissions
- clients
- contacts
- meeting_logs
- vehicles
- drivers
- pools
- pool_staff
- bookings
- driver_assignments
- purchase_orders
- invoices
- payments
- e_vouchers
- maintenance_logs
- report_snapshots
- driver_attendances
- driver_licenses
- driver_contracts optional only

## 5. Business Flow Utama

Sales membuat booking → order masuk Pool → Head Pool/Pool Staff assign driver + kendaraan → booking confirmed → PO → invoice → payment → dashboard GM update.

## 6. Rule Penting

- HR tidak boleh muncul di dashboard utama.
- HR hanya Super Admin `/admin/hr/*`.
- Pool tidak boleh akses Finance/CRM detail sensitif.
- Finance tidak boleh assign driver.
- Sales tidak boleh edit vehicle operational status langsung.
- Semua perubahan status kendaraan harus masuk log.
- Breadcrumb harus menjaga konteks drill-down.

## 7. Definition of Done MVP

MVP dianggap siap demo jika login multi-role berjalan, Sales bisa create booking, Pool bisa assign driver + vehicle, Finance bisa membuat invoice/payment, GM dashboard menampilkan KPI, drill-down berjalan, dan seeder demo menghasilkan data realistis.
