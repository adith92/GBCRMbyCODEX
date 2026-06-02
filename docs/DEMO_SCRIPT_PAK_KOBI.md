# Demo Script Pak Kobi

## 1. Login Accounts

Semua akun demo menggunakan password: `password`

- `superadmin@blueerp.test` / `password`
- `gm@blueerp.test` / `password`
- `sales@blueerp.test` / `password`
- `salesmanager@blueerp.test` / `password`
- `finance@blueerp.test` / `password`
- `operation@blueerp.test` / `password`
- `headpool@blueerp.test` / `password`
- `poolstaff@blueerp.test` / `password`

## 2. Demo Scenario 1 - GM Dashboard Overview

- Login as `gm@blueerp.test`
- Open `Dashboard`
- Highlight KPI cards:
- active clients
- active bookings
- pending bookings
- available vehicles
- vehicles in PO
- vehicles in maintenance
- outstanding invoices
- overdue invoices
- today pool queue count

## 3. Demo Scenario 2 - Sales Create Booking

- Login as `sales@blueerp.test`
- Open `Bookings`
- Create booking for an active client
- Show booking detail page and pending status

## 4. Demo Scenario 3 - Pool Assign Driver + Vehicle

- Login as `headpool@blueerp.test`
- Open `Pool Queue`
- Pick pending booking
- Assign available vehicle and active driver
- Show booking status becomes `assigned`

## 5. Demo Scenario 4 - Confirm Booking

- Continue from assigned booking
- Confirm booking from booking detail flow
- Show booking status becomes `confirmed`

## 6. Demo Scenario 5 - Finance PO -> Invoice -> Payment

- Login as `finance@blueerp.test`
- Open `Purchase Orders`
- Create PO from confirmed booking
- Approve PO
- Generate invoice from approved PO
- Record partial payment, then full payment
- Show invoice status changes `sent -> partial -> paid`

## 7. Demo Scenario 6 - Drill-down Client -> Booking -> Invoice

- From dashboard open latest booking
- From booking detail open client
- From client open invoice
- From invoice open PO and booking back links

## 8. Demo Scenario 7 - E-Voucher

- Login as `finance@blueerp.test`
- Open `E-Vouchers`
- Show active, used, and expired voucher samples
- Explain invoice payment with `evoucher` method validation

## 9. Demo Scenario 8 - Maintenance Flow

- Login as `operation@blueerp.test`
- Open `Maintenance`
- Create `in_progress` maintenance log
- Show vehicle status becomes `maintenance`
- Complete maintenance and show vehicle returns to `available`

## 10. Demo Scenario 9 - HR License/Attendance Super-admin Only

- Login as `superadmin@blueerp.test`
- Open `HR Drivers`, `HR Attendance`, and `HR Licenses`
- Show expired and expiring license data
- Show attendance records with `present`, `sick`, and `leave`
- Mention that non-super-admin roles are blocked from HR routes and HR menu

## 11. Closing Value Proposition

- One Laravel monolith for CRM, Fleet, Pool, Finance, Maintenance, and restricted HR backend workflows
- Role-based access already enforced for demo scenarios
- Demo data is seeded and ready for stakeholder walkthrough before UI polish and Railway deployment
