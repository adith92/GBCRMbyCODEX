# CHECKPOINT_CURRENT.md

## Current Checkpoint

**Checkpoint ID:** PHASE-2.1B-RUNTIME-STABILIZED  
**Date:** 2026-06-02  
**Status:** Completed  
**Branch:** main

## Root Cause Analysis

Runtime blocker pada checkpoint 2.1 bukan berasal dari fitur Booking/Pool business flow, melainkan dari kombinasi issue runtime environment + test/build stability:

1. `php artisan ...` sempat hang/fatal karena environment PHP CLI memakai `max_execution_time=0`.
   - Pada environment PHP 8.5.6 ini, nilai `0` memicu perilaku tidak stabil untuk command CLI Laravel (hang / fatal timeout behavior).
2. Beberapa percobaan validasi sebelumnya dijalankan paralel (`optimize:clear`, `migrate`, `test`, `build`) sehingga memicu lock dan gejala hang palsu.
3. `BookingPoolDispatchTest` menggunakan fixture dari data seed random (`pool/driver/vehicle/status`) sehingga tidak deterministik dan memicu `ModelNotFoundException` intermiten.

## Fixes Implemented

1. **Artisan runtime guard**
   - File: `artisan`
   - Menambahkan guard:
     - jika `max_execution_time <= 0`, set ke `3600` untuk CLI command.
   - Ini menstabilkan `php artisan` command pada environment ini.

2. **Build stabilization (Vite refresh watcher during build)**
   - File: `vite.config.js`
   - Mengubah konfigurasi `refresh` supaya nonaktif saat command `build`:
     - `refresh: !isBuildCommand`
   - Mencegah proses build yang tertahan watcher di mode production build.

3. **Booking dispatch tests hardened (deterministic fixtures)**
   - File: `tests/Feature/BookingPoolDispatchTest.php`
   - Fixture assignment diubah jadi deterministic:
     - membuat `pool/client/booking/vehicle/driver` sendiri per test, status eksplisit.
   - Assertion error key disesuaikan dengan validasi aktual (`vehicle_id` vs `driver_id`).
   - Menutup failure acak `ModelNotFoundException` dari data random seeder.

## Validation Result

Berikut command validasi yang dijalankan untuk checkpoint ini:

1. `composer install`
   - dependency lock verified, no package changes needed.
2. `npm install`
   - success, up to date.
3. `php artisan optimize:clear`
   - success.
4. `php artisan migrate:fresh --seed`
   - success.
5. `npm run build`
   - success.
6. `php artisan test`
   - success.
   - Result: **54 passed, 0 failed**.

## Next Recommended Checkpoint

Proceed to **Checkpoint 2.2 — Finance Flow**:

1. Purchase Orders baseline
2. Invoices baseline
3. Payments baseline
4. eVouchers baseline
5. finance permission-flow validation

## Notes

- Fitur Booking + Pool Dispatch tetap dipertahankan (tidak dihapus).
- Fokus checkpoint ini murni runtime stabilization + full validation pass.
