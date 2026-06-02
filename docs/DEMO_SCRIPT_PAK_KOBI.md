# Demo Script Pak Kobi

## 1. Login Accounts

- `superadmin@blueerp.test` / `password`
- `gm@blueerp.test` / `password`
- `sales@blueerp.test` / `password`
- `salesmanager@blueerp.test` / `password`
- `finance@blueerp.test` / `password`
- `operation@blueerp.test` / `password`
- `headpool@blueerp.test` / `password`
- `poolstaff@blueerp.test` / `password`

## 2. Demo Scenario 1 - Sales Create Booking

- Login as `sales@blueerp.test`
- Open `Bookings`
- Create booking for an active client
- Show booking detail page and pending status

## 3. Demo Scenario 2 - Pool Assign Driver + Vehicle

- Login as `headpool@blueerp.test`
- Open `Pool Queue`
- Pick pending booking
- Assign available vehicle and active driver
- Show booking status becomes `assigned`

## 4. Demo Scenario 3 - Finance PO -> Invoice -> Payment

- Login as `finance@blueerp.test`
- Open `Purchase Orders`
- Create PO from confirmed booking
- Approve PO
- Generate invoice from approved PO
- Record partial payment, then full payment
- Show invoice status changes `sent -> partial -> paid`

## 5. Demo Scenario 4 - GM Dashboard

- Login as `gm@blueerp.test`
- Open dashboard
- Highlight KPI cards:
- active clients
- active bookings
- available vehicles
- maintenance vehicles
- outstanding invoices
- overdue invoices

## 6. Demo Scenario 5 - Drill-down

- From dashboard open latest booking
- From booking detail open client
- From client open invoice
- From invoice open PO and booking back links

## 7. Demo Scenario 6 - Maintenance

- Login as `operation@blueerp.test`
- Open `Maintenance`
- Create `in_progress` maintenance log
- Show vehicle status becomes `maintenance`
- Complete maintenance and show vehicle returns to `available`

## 8. Demo Scenario 7 - Driver Contingency Basic

- Login as `superadmin@blueerp.test` or `headpool@blueerp.test`
- Show HR attendance with driver marked `sick`
- Explain pool can switch assignment to another active driver
