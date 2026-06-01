<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::view('/crm', 'module-placeholder', [
        'header' => 'CRM Module',
        'message' => 'Checkpoint 0.2 placeholder. CRM workflows will be implemented in Phase 1.2.',
    ])->middleware('permission:clients.view|meeting-logs.view')->name('crm.index');

    Route::view('/fleet', 'module-placeholder', [
        'header' => 'Fleet Module',
        'message' => 'Checkpoint 0.2 placeholder. Fleet CRUD and vehicle state machine start in Phase 1.1.',
    ])->middleware('permission:vehicles.view')->name('fleet.index');

    Route::view('/drivers', 'module-placeholder', [
        'header' => 'Driver Module',
        'message' => 'Checkpoint 0.2 placeholder. Driver management foundation starts in Phase 1.1.',
    ])->middleware('permission:drivers.view')->name('drivers.index');

    Route::view('/pool', 'module-placeholder', [
        'header' => 'Pool Module',
        'message' => 'Checkpoint 0.2 placeholder. Pool assignment flow starts in Phase 2.1.',
    ])->middleware('permission:pool.view-all|pool.view-own')->name('pool.index');

    Route::view('/bookings', 'module-placeholder', [
        'header' => 'Booking Module',
        'message' => 'Checkpoint 0.2 placeholder. Booking flow starts in Phase 2.1.',
    ])->middleware('permission:bookings.view')->name('bookings.index');

    Route::view('/finance', 'module-placeholder', [
        'header' => 'Finance Module',
        'message' => 'Checkpoint 0.2 placeholder. PO, invoice, payment, and e-voucher flows start in Phase 2.2.',
    ])->middleware('permission:purchase-orders.view|invoices.view|payments.view|evouchers.view')->name('finance.index');

    Route::view('/maintenance', 'module-placeholder', [
        'header' => 'Maintenance Module',
        'message' => 'Checkpoint 0.2 placeholder. Maintenance workflows start in Phase 3.1.',
    ])->middleware('permission:maintenance.view')->name('maintenance.index');

    Route::view('/reports', 'module-placeholder', [
        'header' => 'Reports Module',
        'message' => 'Checkpoint 0.2 placeholder. Reporting dashboard and exports start in Phase 3.1.',
    ])->middleware('permission:reports.view')->name('reports.index');

    Route::prefix('/admin/hr')->name('admin.hr.')->middleware('role:super-admin')->group(function (): void {
        Route::view('/', 'module-placeholder', [
            'header' => 'HR Admin Module',
            'message' => 'Checkpoint 0.2 placeholder. HR backend-only module starts in Phase 3.1.',
        ])->name('index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
