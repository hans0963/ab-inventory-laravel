<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DiscountTypeController;

use App\Http\Controllers\InventoryStatusController;
use App\Http\Controllers\InventoryMovementsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\FinancialOverviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ProductionInController;
use App\Http\Controllers\ProductionManagementController;
use App\Http\Controllers\ProductionOutController;
use App\Http\Controllers\StockWithdrawalController;
use App\Http\Controllers\InventoryReceivingController;

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
Route::middleware(['auth', 'verified'])->group(function () {
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
        Route::post('purchases/{purchase}/link-receiving', [PurchaseController::class, 'linkReceiving'])->name('purchases.link-receiving');
        Route::post('purchases/{purchase}/unlink-receiving', [PurchaseController::class, 'unlinkReceiving'])->name('purchases.unlink-receiving');
        Route::get('purchases/{purchase}/receiving-matching', [PurchaseController::class, 'receivingMatching'])->name('purchases.receiving-matching');
        Route::get('purchases-receiving-progress', [PurchaseController::class, 'receivingProgress'])->name('purchases.receiving-progress');
        Route::post('purchases/{purchase}/mark-received', [PurchaseController::class, 'markAsReceived'])->name('purchases.mark-received');
        Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    });

    Route::middleware(['can:view-inventory'])->group(function () {
        Route::get('/inventory', [InventoryStatusController::class, 'index'])->name('inventory.status');
        Route::delete('/inventory-movements/{id}', [InventoryMovementsController::class, 'destroy'])->name('inventory_movements.destroy');
    });
});

// Cashier, Manager & Admin Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['can:view-products'])->group(function () {
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::resource('products', ProductController::class);
    });

    Route::middleware(['can:view-sales-orders'])->group(function () {
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
        
        // Phase 1: Enhanced Sales Routes
        Route::resource('sales', SaleController::class);
        Route::get('/sales/{sale}/print', [SaleController::class, 'printReceipt'])->name('sales.print');
    });

    // Phase 1: Discount Types Management (Manager/Admin)
    Route::middleware(['can:view-discounts'])->group(function () {
        Route::resource('discounts', DiscountTypeController::class);
    });

    Route::middleware(['can:view-customers'])->group(function () {
        Route::resource('customers', CustomerController::class);
    });
});


Route::get('/sales-report', [ReportController::class, 'salesReport'])->middleware(['auth', 'verified', 'can:view-manager-sales-report'])->name('inventory.sales');
Route::get('/inventory-report', [ReportController::class, 'inventoryReport'])->middleware(['auth', 'verified', 'can:view-inventory'])->name('reports.inventory');
Route::get('/production-reports', [ReportController::class, 'productionReports'])->middleware(['auth', 'verified', 'can:view-production-in'])->name('reports.production');
Route::get('/financial-overview', [FinancialOverviewController::class, 'index'])->middleware(['auth', 'verified', 'can:view-reports'])->name('financial.overview');

// Cashier Module Routes
Route::get('/sales-orders', [OrderController::class, 'index'])->middleware(['auth', 'verified', 'role:cashier'])->name('sales-orders.index');

// Manager Module Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:manager,admin'])->group(function () {
        Route::get('raw-materials/in', [RawMaterialController::class, 'stockInView'])->name('raw-materials.in');
        Route::post('raw-materials/in', [RawMaterialController::class, 'processStockIn'])->name('raw-materials.processIn');
        Route::get('raw-materials/out', [RawMaterialController::class, 'stockOutView'])->name('raw-materials.out');
        Route::post('raw-materials/out', [RawMaterialController::class, 'processStockOut'])->name('raw-materials.processOut');
        Route::post('raw-materials/{rawMaterial}/adjust', [RawMaterialController::class, 'adjust'])->name('raw-materials.adjust');
        Route::resource('raw-materials', RawMaterialController::class);
        Route::get('production-management', [ProductionManagementController::class, 'index'])->name('production-management.index');
        Route::resource('production-in', ProductionInController::class);
        Route::post('production-in/{productionIn}/approve', [ProductionInController::class, 'approve'])->name('production-in.approve');
        Route::post('production-in/{productionIn}/reject', [ProductionInController::class, 'reject'])->name('production-in.reject');
        Route::resource('production-out', ProductionOutController::class);
        Route::post('production-out/{productionOut}/approve', [ProductionOutController::class, 'approve'])->name('production-out.approve');
        Route::post('production-out/{productionOut}/reject', [ProductionOutController::class, 'reject'])->name('production-out.reject');
        Route::resource('stock-withdrawal', StockWithdrawalController::class);
        Route::post('stock-withdrawal/{withdrawal}/approve', [StockWithdrawalController::class, 'approve'])->name('stock-withdrawal.approve');
        Route::resource('inventory-receiving', InventoryReceivingController::class);
        Route::post('inventory-receiving/{inventoryReceiving}/approve', [InventoryReceivingController::class, 'approve'])->name('inventory-receiving.approve');
        Route::post('inventory-receiving/{inventoryReceiving}/reject', [InventoryReceivingController::class, 'reject'])->name('inventory-receiving.reject');
        Route::post('stock-withdrawal/{withdrawal}/reject', [StockWithdrawalController::class, 'reject'])->name('stock-withdrawal.reject');
        
        Route::get('/manager-sales-report', [ReportController::class, 'salesReport'])->name('manager-sales-report.index');
        Route::get('/manager-reports', [ReportController::class, 'managerReports'])->name('manager-reports.index');
    });
});

// Admin Module Routes
Route::get('/admin-sales-report', [ReportController::class, 'salesReport'])->middleware(['auth', 'verified', 'role:admin'])->name('sales-report.index');

Route::get('/admin-reports', [ReportController::class, 'adminReports'])->middleware(['auth', 'verified', 'role:admin'])->name('reports.index');

require __DIR__.'/auth.php';
