<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemberController;

// Redirect ke login jika belum login
Route::get('/', function () {
    return redirect()->route('login');
});

// Login dan Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ✅ Dashboard menggunakan controller
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk role Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Manajemen Produk
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Update stok produk
    Route::get('products/{product}/stok', [ProductController::class, 'editstok'])->name('products.editstok');
    Route::put('products/{product}/stok', [ProductController::class, 'updatestok'])->name('products.updatestok');

    // Manajemen User
    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::get('user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
});

// Route untuk role Petugas
Route::middleware(['auth', 'role:petugas'])->group(function () {
    //product
    Route::get('products', [ProductController::class, 'index'])->name('products.index');

    // Pembelian
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases/confirm', [PurchaseController::class, 'confirm'])->name('purchases.confirm');
    Route::get('/purchases/member', [PurchaseController::class, 'memberForm'])->name('purchases.member');
    Route::post('/purchases/finish', [PurchaseController::class, 'finish'])->name('purchases.finish');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/purchases/receipt/{id}', [PurchaseController::class, 'receipt'])->name('purchases.receipt');
    Route::get('/purchases/{id}/download', [PurchaseController::class, 'downloadReceipt'])->name('purchases.download');

    // Cek history dan poin member
    Route::get('/check-member-history', [MemberController::class, 'checkMemberHistory'])->name('members.check-history');

    // Export data pembelian ke Excel
    Route::get('/purchases/export/excel', [PurchaseController::class, 'exportExcel'])->name('purchases.export.excel');
});
