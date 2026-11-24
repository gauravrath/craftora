<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MediaController;

Route::get('/', function () {
    return view('auth.login');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
Route::put('/category/{category}', [CategoryController::class, 'update'])->name('category.update');
Route::get('/category/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
Route::delete('/category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');  
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('category.view');
Route::patch('/category/{category}/status', [CategoryController::class, 'changeStatus'])->name('category.status');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/create', [ProductController::class, 'create']);
Route::post('/product/store', [ProductController::class, 'store']);
Route::get('/product/bulk-upload', [ProductController::class, 'bulkUpload']);
Route::get('/product/bulk-upload/sample-download', [ProductController::class, 'sampleDownload']);
Route::get('/product/{product}', [ProductController::class, 'show']);
Route::get('/product/{product}/edit', [ProductController::class, 'edit']);
Route::put('/product/{product}', [ProductController::class, 'update']);
Route::delete('/product/{product}', [ProductController::class, 'destroy']);
Route::get('/products/import', [ProductController::class, 'index'])->name('products.import');
Route::post('/products/import/save', [ProductController::class, 'save'])->name('products.import.save');
Route::patch('/product/{product}/status', [ProductController::class, 'changeStatus'])->name('product.status');

Route::get('/orders', [OrderController::class,'index'])->name('orders.index');
Route::get('/orders/{id}', [OrderController::class,'show'])->name('orders.show');
Route::patch('/orders/{id}/status', [OrderController::class,'updateStatus'])->name('orders.updateStatus');
Route::delete('/orders/{id}', [OrderController::class,'destroy'])->name('orders.destroy');


Route::get('/customers', [CustomersController::class,'index'])->name('customers.index');
Route::get('/customers/{id}', [CustomersController::class,'show'])->name('customers.show');
Route::patch('/customers/{id}/toggle-status', [CustomersController::class,'toggleStatus'])->name('customers.toggleStatus');
Route::resource('coupons', CouponController::class);
Route::get('/reports', [ReportsController::class, 'index']);


Route::get('/media', [MediaController::class, 'index']);
Route::post('/media/upload', [MediaController::class, 'upload']);
Route::delete('/media/{id}', [MediaController::class, 'delete']);


require __DIR__.'/auth.php';

//font-family: Arial, Helvetica, sans-serif;
