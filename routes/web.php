<?php

use App\Http\Controllers\ClientContactController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DemoEnvironmentController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MeetingLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VendorPartnerController;
use App\Http\Controllers\VehicleController;
use App\Livewire\Admin\Hr\Attendance as HrAttendance;
use App\Livewire\Admin\Hr\Drivers as HrDrivers;
use App\Livewire\Admin\Hr\Licenses as HrLicenses;
use App\Livewire\Bookings\Create as BookingCreate;
use App\Livewire\Bookings\Edit as BookingEdit;
use App\Livewire\Bookings\Index as BookingIndex;
use App\Livewire\Bookings\Show as BookingShow;
use App\Livewire\Finance\Dashboard as FinanceDashboard;
use App\Livewire\Finance\EVouchers\Create as EVoucherCreate;
use App\Livewire\Finance\EVouchers\Index as EVoucherIndex;
use App\Livewire\Finance\EVouchers\Show as EVoucherShow;
use App\Livewire\Finance\Invoices\Index as InvoiceIndex;
use App\Livewire\Finance\Invoices\Show as InvoiceShow;
use App\Livewire\Finance\PurchaseOrders\Create as PurchaseOrderCreate;
use App\Livewire\Finance\PurchaseOrders\Edit as PurchaseOrderEdit;
use App\Livewire\Finance\PurchaseOrders\Index as PurchaseOrderIndex;
use App\Livewire\Finance\PurchaseOrders\Show as PurchaseOrderShow;
use App\Livewire\Maintenance\Create as MaintenanceCreate;
use App\Livewire\Maintenance\Edit as MaintenanceEdit;
use App\Livewire\Maintenance\Index as MaintenanceIndex;
use App\Livewire\Maintenance\Show as MaintenanceShow;
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

    Route::get('/search', SearchController::class)->name('search.index');
    Route::get('/activity', ActivityController::class)->name('activity.index');
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/{user}/performance', [SalesController::class, 'performance'])->name('sales.performance');
    Route::post('/demo/switch-role', [DemoEnvironmentController::class, 'switchRole'])->name('demo.switch-role');
    Route::post('/demo/reset', [DemoEnvironmentController::class, 'reset'])->name('demo.reset');

    Route::prefix('/crm')->name('crm.')->group(function (): void {
        Route::get('/', fn () => redirect()->route('crm.clients.index'))
            ->middleware('permission:clients.view|meeting-logs.view')
            ->name('index');

        Route::resource('clients', ClientController::class)
            ->middlewareFor(['index', 'show'], 'permission:clients.view')
            ->middlewareFor(['create', 'store'], 'permission:clients.create')
            ->middlewareFor(['edit', 'update'], 'permission:clients.update')
            ->middlewareFor(['destroy'], 'permission:clients.delete');

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
            ->middlewareFor(['index', 'show'], 'permission:vehicles.view')
            ->middlewareFor(['create', 'store'], 'permission:vehicles.create')
            ->middlewareFor(['edit', 'update'], 'permission:vehicles.update')
            ->middlewareFor(['destroy'], 'permission:vehicles.delete');
    });

    Route::prefix('/partners')->name('partners.')->group(function (): void {
        Route::get('/', fn () => redirect()->route('partners.vendors.index'))
            ->middleware('permission:clients.view')
            ->name('index');

        Route::resource('vendors', VendorPartnerController::class)
            ->parameter('vendors', 'vendor')
            ->middlewareFor(['index', 'show'], 'permission:clients.view')
            ->middlewareFor(['create', 'store'], 'permission:clients.create')
            ->middlewareFor(['edit', 'update'], 'permission:clients.update');
    });

    Route::resource('/drivers', DriverController::class)
        ->middlewareFor(['index', 'show'], 'permission:drivers.view')
        ->middlewareFor(['create', 'store'], 'permission:drivers.create')
        ->middlewareFor(['edit', 'update'], 'permission:drivers.update')
        ->middlewareFor(['destroy'], 'permission:drivers.delete');

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

    Route::prefix('/finance')->name('finance.')->group(function (): void {
        Route::get('/', FinanceDashboard::class)
            ->middleware('permission:purchase-orders.view|invoices.view|payments.view|evouchers.view')
            ->name('index');

        Route::prefix('/purchase-orders')->name('purchase-orders.')->group(function (): void {
            Route::get('/', PurchaseOrderIndex::class)->middleware('permission:purchase-orders.view')->name('index');
            Route::get('/create', PurchaseOrderCreate::class)->middleware('permission:purchase-orders.create')->name('create');
            Route::get('/{purchaseOrder}', PurchaseOrderShow::class)->middleware('permission:purchase-orders.view')->name('show');
            Route::get('/{purchaseOrder}/edit', PurchaseOrderEdit::class)->middleware('permission:purchase-orders.create')->name('edit');
        });

        Route::prefix('/invoices')->name('invoices.')->group(function (): void {
            Route::get('/', InvoiceIndex::class)->middleware('permission:invoices.view')->name('index');
            Route::get('/{invoice}', InvoiceShow::class)->middleware('permission:invoices.view')->name('show');
        });

        Route::prefix('/e-vouchers')->name('e-vouchers.')->group(function (): void {
            Route::get('/', EVoucherIndex::class)->middleware('permission:evouchers.view')->name('index');
            Route::get('/create', EVoucherCreate::class)->middleware('permission:evouchers.create')->name('create');
            Route::get('/{eVoucher}', EVoucherShow::class)->middleware('permission:evouchers.view')->name('show');
        });
    });

    Route::prefix('/maintenance')->name('maintenance.')->group(function (): void {
        Route::get('/', MaintenanceIndex::class)->middleware('permission:maintenance.view')->name('index');
        Route::get('/create', MaintenanceCreate::class)->middleware('permission:maintenance.create')->name('create');
        Route::get('/{maintenanceLog}', MaintenanceShow::class)->middleware('permission:maintenance.view')->name('show');
        Route::get('/{maintenanceLog}/edit', MaintenanceEdit::class)->middleware('permission:maintenance.update')->name('edit');
    });

    Route::get('/reports', ReportsController::class)
        ->middleware('permission:reports.view')
        ->name('reports.index');

    Route::prefix('/admin/hr')->name('admin.hr.')->middleware('role:super-admin')->group(function (): void {
        Route::get('/', fn () => redirect()->route('admin.hr.drivers'))->name('index');
        Route::get('/drivers', HrDrivers::class)->name('drivers');
        Route::get('/attendance', HrAttendance::class)->name('attendance');
        Route::get('/licenses', HrLicenses::class)->name('licenses');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
