<?php

use App\Http\Controllers\ClientContactController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\MeetingLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Livewire\Bookings\Create as BookingCreate;
use App\Livewire\Bookings\Edit as BookingEdit;
use App\Livewire\Bookings\Index as BookingIndex;
use App\Livewire\Bookings\Show as BookingShow;
use App\Livewire\Pool\AssignBooking;
use App\Livewire\Pool\Queue as PoolQueue;
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

    Route::prefix('/crm')->name('crm.')->group(function (): void {
        Route::get('/', fn () => redirect()->route('crm.clients.index'))
            ->middleware('permission:clients.view|meeting-logs.view')
            ->name('index');

        Route::resource('clients', ClientController::class)
            ->middleware('permission:clients.view', ['only' => ['index', 'show']])
            ->middleware('permission:clients.create', ['only' => ['create', 'store']])
            ->middleware('permission:clients.update', ['only' => ['edit', 'update']])
            ->middleware('permission:clients.delete', ['only' => ['destroy']]);

        Route::post('clients/{client}/contacts', [ClientContactController::class, 'store'])
            ->middleware('permission:clients.update')
            ->name('clients.contacts.store');
        Route::put('clients/{client}/contacts/{contact}', [ClientContactController::class, 'update'])
            ->middleware('permission:clients.update')
            ->name('clients.contacts.update');
        Route::delete('clients/{client}/contacts/{contact}', [ClientContactController::class, 'destroy'])
            ->middleware('permission:clients.update')
            ->name('clients.contacts.destroy');

        Route::post('clients/{client}/meeting-logs', [MeetingLogController::class, 'store'])
            ->middleware('permission:meeting-logs.create')
            ->name('clients.meeting-logs.store');
    });

    Route::prefix('/fleet')->name('fleet.')->group(function (): void {
        Route::get('/', fn () => redirect()->route('fleet.vehicles.index'))
            ->middleware('permission:vehicles.view')
            ->name('index');

        Route::resource('vehicles', VehicleController::class)
            ->middleware('permission:vehicles.view', ['only' => ['index', 'show']])
            ->middleware('permission:vehicles.create', ['only' => ['create', 'store']])
            ->middleware('permission:vehicles.update', ['only' => ['edit', 'update']])
            ->middleware('permission:vehicles.delete', ['only' => ['destroy']]);
    });

    Route::resource('/drivers', DriverController::class)
        ->middleware('permission:drivers.view', ['only' => ['index', 'show']])
        ->middleware('permission:drivers.create', ['only' => ['create', 'store']])
        ->middleware('permission:drivers.update', ['only' => ['edit', 'update']])
        ->middleware('permission:drivers.delete', ['only' => ['destroy']]);

    Route::get('/bookings', BookingIndex::class)
        ->middleware('permission:bookings.view')
        ->name('bookings.index');
    Route::get('/bookings/create', BookingCreate::class)
        ->middleware('permission:bookings.create')
        ->name('bookings.create');
    Route::get('/bookings/{booking}', BookingShow::class)
        ->middleware('permission:bookings.view')
        ->name('bookings.show');
    Route::get('/bookings/{booking}/edit', BookingEdit::class)
        ->middleware('permission:bookings.update')
        ->name('bookings.edit');

    Route::get('/pool', fn () => redirect()->route('pool.queue'))
        ->middleware('permission:pool.view-all|pool.view-own')
        ->name('pool.index');
    Route::get('/pool/queue', PoolQueue::class)
        ->middleware('permission:pool.view-all|pool.view-own')
        ->name('pool.queue');
    Route::get('/pool/bookings/{booking}/assign', AssignBooking::class)
        ->middleware('permission:pool.assign-driver')
        ->name('pool.assign');

    Route::view('/finance', 'module-placeholder', [
        'header' => 'Finance Module',
        'message' => 'Checkpoint placeholder. PO, invoice, payment, and e-voucher flows start in Phase 2.2.',
    ])->middleware('permission:purchase-orders.view|invoices.view|payments.view|evouchers.view')->name('finance.index');

    Route::view('/maintenance', 'module-placeholder', [
        'header' => 'Maintenance Module',
        'message' => 'Checkpoint placeholder. Maintenance workflows start in Phase 3.1.',
    ])->middleware('permission:maintenance.view')->name('maintenance.index');

    Route::view('/reports', 'module-placeholder', [
        'header' => 'Reports Module',
        'message' => 'Checkpoint placeholder. Reporting dashboard and exports start in Phase 3.1.',
    ])->middleware('permission:reports.view')->name('reports.index');

    Route::prefix('/admin/hr')->name('admin.hr.')->middleware('role:super-admin')->group(function (): void {
        Route::view('/', 'module-placeholder', [
            'header' => 'HR Admin Module',
            'message' => 'Checkpoint placeholder. HR backend-only module starts in Phase 3.1.',
        ])->name('index');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
