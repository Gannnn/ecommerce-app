<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminCurrencyController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminCategoryController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use Illuminate\Support\Facades\Route;

// Categories (public)
Route::get('/categories', [CategoryController::class, 'index']);

// Currencies (public — active only)
Route::get('/currencies', [CurrencyController::class, 'index']);

// User auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Products (public)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Cart — public routes, controller resolves user via auth('sanctum') if token present
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'store']);
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);
Route::delete('/cart', [CartController::class, 'destroyAll']);

// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});

// Admin auth (public — no token required for login)
Route::post('/admin/auth/login', [AdminAuthController::class, 'login']);

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('auth/logout', [AdminAuthController::class, 'logout']);
    Route::get('auth/me', [AdminAuthController::class, 'me']);

    Route::apiResource('products', AdminProductController::class);
    Route::apiResource('categories', AdminCategoryController::class)->except(['show']);
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::patch('orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    Route::get('currencies', [AdminCurrencyController::class, 'index']);
    Route::patch('currencies/{id}', [AdminCurrencyController::class, 'update']);
    Route::post('currencies/sync', [AdminCurrencyController::class, 'sync']);
});
