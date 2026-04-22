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

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('employees', EmployeeController::class);

Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');

Route::resource('customers', CustomerController::class);

Route::resource('suppliers', SupplierController::class);
Route::resource('purchases', PurchaseController::class);

Route::get('/inventory', [InventoryStatusController::class, 'index'])->name('inventory.status');
Route::delete('/inventory-movements/{id}', [InventoryMovementsController::class, 'destroy'])->name('inventory_movements.destroy');

Route::get('/sales-report', [InventoryStatusController::class, 'salesReport'])->name('inventory.sales');


require __DIR__.'/auth.php';
