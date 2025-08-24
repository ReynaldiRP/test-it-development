<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);

    // Location API routes
    Route::get('api/cities/{provinceCode}', [CustomerController::class, 'getCities'])->name('api.cities');
    Route::get('api/districts/{cityCode}', [CustomerController::class, 'getDistricts'])->name('api.districts');
    Route::get('api/subdistricts/{districtCode}', [CustomerController::class, 'getSubdistricts'])->name('api.subdistricts');

    // Transaction API routes
    Route::get('api/customers/{customerId}', [TransactionController::class, 'getCustomerDetails'])->name('api.customers.details');
    Route::get('api/products/{productId}', [TransactionController::class, 'getProductDetails'])->name('api.products.details');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
