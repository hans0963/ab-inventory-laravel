<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;

use App\Http\Controllers\InventoryStatusController;
use App\Http\Controllers\InventoryMovementsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\FinancialOverviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ProductionInController;
use App\Http\Controllers\ProductionOutController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Manager & Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::middleware(['can:view-categories'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    Route::middleware(['can:view-employees'])->group(function () {
        Route::resource('employees', EmployeeController::class);
    });

    Route::middleware(['can:view-suppliers'])->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    Route::middleware(['can:view-purchases'])->group(function () {
        Route::resource('purchases', PurchaseController::class);
    });

    Route::middleware(['can:view-inventory'])->group(function () {
        Route::get('/inventory', [InventoryStatusController::class, 'index'])->name('inventory.status');
        Route::delete('/inventory-movements/{id}', [InventoryMovementsController::class, 'destroy'])->name('inventory_movements.destroy');
    });
});

// Cashier, Manager & Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::middleware(['can:view-products'])->group(function () {
        Route::resource('products', ProductController::class);
    });

    Route::middleware(['can:view-sales-orders'])->group(function () {
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    });

    Route::middleware(['can:view-customers'])->group(function () {
        Route::resource('customers', CustomerController::class);
    });
});

Route::get('/sales-report', [ReportController::class, 'salesReport'])->middleware(['auth'])->name('inventory.sales');
Route::get('/financial-overview', [FinancialOverviewController::class, 'index'])->middleware(['auth'])->name('financial.overview');

// Cashier Module Routes
Route::get('/sales-orders', [OrderController::class, 'index'])->middleware(['auth', 'role:cashier'])->name('sales-orders.index');

// Manager Module Routes
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::resource('raw-materials', RawMaterialController::class);
    Route::resource('production-in', ProductionInController::class);
    Route::resource('production-out', ProductionOutController::class);
    
    Route::get('/manager-sales-report', [ReportController::class, 'salesReport'])->name('manager-sales-report.index');
    Route::get('/manager-reports', [ReportController::class, 'managerReports'])->name('manager-reports.index');
});

// Admin Module Routes
Route::get('/admin-sales-report', [ReportController::class, 'salesReport'])->middleware(['auth', 'role:admin'])->name('sales-report.index');

Route::get('/admin-reports', [ReportController::class, 'adminReports'])->middleware(['auth', 'role:admin'])->name('reports.index');

require __DIR__.'/auth.php';
