# PROJECT_PRD.md — BlueERP MVP Product Requirement Document

## 1. Problem

Tim B2B fleet membutuhkan sistem terpadu untuk melihat client, booking, kendaraan, driver, pool, invoice, payment, dan status operasional tanpa berpindah-pindah file/manual spreadsheet.

## 2. Users / Roles

| Role | Tujuan | Akses |
|---|---|---|
| GM | Lihat KPI dan performa | Dashboard full summary |
| Sales | Kelola client, meeting, booking | CRM + Booking |
| Sales Manager | Approve booking/PO tertentu | Sales + approval |
| Finance | PO, Invoice, Payment | Finance module |
| Operation | Maintenance + vehicle state | Fleet/Ops |
| Head Pool | Semua pool + assign driver | Pool all |
| Pool Staff | Pool sendiri | Pool own |
| Super Admin | Semua + HR backend | Full system |

## 3. Core Requirements

### Auth & RBAC

- Login required.
- Role-based sidebar.
- Permission-based route protection.
- Seeder roles + permission awal.

### CRM

- Client CRUD.
- Contact person.
- Meeting logs.
- Client tier: bronze/silver/gold/platinum.
- Client detail menampilkan booking history dan invoice list.

### Fleet

- Vehicle CRUD.
- Status: available, po, maintenance, hold.
- State transition validation.
- Vehicle detail dengan booking history, maintenance logs, driver assignment.

### Pool

- Pool master data.
- Head Pool dapat lihat semua pool.
- Pool Staff hanya pool sendiri.
- Order masuk dari Sales.
- Assign driver + vehicle.
- Driver contingency assignment: primary, substitute, temporary.

### Booking

- Sales create booking.
- Booking status: pending, confirmed, completed, cancelled.
- Pool assignment wajib sebelum confirmed.
- Link ke client, vehicle, driver, PO.

### Finance

- PO dibuat dari booking.
- Invoice dibuat dari PO.
- Payment bisa partial/full.
- Payment method termasuk e-voucher.
- Overdue invoice visible di dashboard.

### HR Backend-only

- `/admin/hr/drivers`
- `/admin/hr/attendance`
- `/admin/hr/licenses`
- Tidak muncul di dashboard utama.
- Hanya Super Admin.

### Multi-level Drill-down

Dashboard → Fleet Status → Vehicle Detail → Booking Detail → Client Detail → Invoice Detail → Back ke Client Detail.

## 4. Non-functional Requirements

- Mobile responsive target 375px.
- UI clean enterprise.
- Seeder demo harus realistis.
- Migrations repeatable.
- No hardcoded production secrets.
- Audit trail untuk status penting.

## 5. Acceptance Criteria MVP Demo

- 50+ dummy records tersedia.
- Skenario demo booking baru berjalan end-to-end.
- Skenario drill-down berjalan.
- Skenario driver substitute berjalan.
- Skenario maintenance berjalan.
- Dashboard GM menampilkan KPI yang berubah sesuai data.
