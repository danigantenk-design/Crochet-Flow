<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Route Publik (Bisa diakses tanpa login/token)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// 2. Route Terproteksi (Wajib pakai Bearer Token di Postman)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pesanan & Digital Download untuk Mobile
    Route::get('/orders', [OrderController::class, 'apiHistory']);
    Route::post('/orders/checkout', [OrderController::class, 'apiStore']);
    Route::post('/orders/{id}/upload-payment', [OrderController::class, 'apiUploadPayment']);
    Route::get('/orders/{orderItemId}/download', [OrderController::class, 'apiDownload']);

    Route::get('/products/category/{categoryId}', [ProductController::class, 'byCategory']); // Produk berdasarkan kategori
    Route::get('/products/shop/{shopId}', [ProductController::class, 'byShop']); // Produk berdasarkan toko
    Route::get('/products/search/{keyword}', [ProductController::class, 'search']); // Cari produk berdasarkan keyword
    Route::get('/products/featured', [ProductController::class, 'featured']); // Produk unggulan

    // ALUR CHECKOUT & PESANAN
    // 1. Membuat pesanan baru (Checkout)
    Route::post('/orders/checkout', [OrderController::class, 'apiStore']); 
    
    // 2. Mengambil riwayat pesanan (History)
    Route::get('/orders/history', [OrderController::class, 'apiHistory']); 
    
    // 3. Mengunggah bukti transfer
    Route::post('/orders/{id}/upload-payment', [OrderController::class, 'apiUploadPayment']); 

    // 4. Fitur tambahan: Download pola digital (jika status sudah 'completed')
    Route::get('/orders/{orderItemId}/download', [OrderController::class, 'apiDownload']);
    
    // Profil Pengguna
    Route::post('/user/update', [AuthController::class, 'updateProfile']); // Jika ingin fitur ganti nama/foto profil

    // Toko
    Route::get('/shops', [ShopController::class, 'apiIndex']);
    Route::get('/shops/{id}', [ShopController::class, 'apiShow']);

    // Keranjang
    Route::get('/cart', [CartController::class, 'apiIndex']);
    Route::post('/cart/add', [CartController::class, 'apiStore']);
    Route::patch('/cart/{id}/update', [CartController::class, 'apiUpdate']);
    Route::delete('/cart/{id}', [CartController::class, 'apiDestroy']);

    Route::post('/orders/checkout', [OrderController::class, 'apiStore']);


});