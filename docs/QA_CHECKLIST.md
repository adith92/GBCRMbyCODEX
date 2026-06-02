# QA Checklist

## Auth

- Login works for all demo users
- Logout works
- Guest redirected to login

## RBAC

- Sidebar follows role permissions
- Unauthorized routes return `403`
- HR only visible to super-admin

## CRM

- Client CRUD works
- Contacts add/update/delete works
- Meeting log add works

## Fleet

- Vehicle CRUD works
- Filters/search work
- Vehicle detail shows booking and maintenance sections

## Driver

- Driver CRUD works
- License indicators render
- Driver detail shows booking section

## Booking

- Booking create/edit/detail works
- Confirm and cancel flow works
- Booking detail links to client, vehicle, driver, PO, invoice

## Pool

- Queue list works
- Assign driver and vehicle works
- Maintenance vehicle cannot be assigned

## Finance

- PO create and approve works
- Invoice create works
- Payment partial/full works
- E-voucher payment validation works

## Maintenance

- Maintenance create/edit/detail works
- `in_progress` changes vehicle to `maintenance`
- `completed/cancelled` can return vehicle to `available`

## HR Access Restriction

- Super-admin can access HR drivers, attendance, licenses
- Finance cannot access HR
- Pool staff cannot access HR

## Dashboard

- KPI cards render
- Latest lists render
- Drill-down links open target entity

## Mobile 375px Quick Check

- Login page usable
- Sidebar/topbar readable
- Main CRUD pages scroll correctly
- Dashboard cards stack cleanly
