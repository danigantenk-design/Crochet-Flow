<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// === DI LUAR MIDDLEWARE AUTH (PUBLIC) ===
// Halaman Profil Toko (Bisa dilihat siapa saja)
Route::get('/shop/{id}', [ShopController::class, 'show'])->name('shop.show');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // DOWNLOAD PRODUK DIGITAL
    // Kita gunakan ID dari OrderItem (bukan Product ID), karena kita perlu cek status pembayaran ordernya
    Route::get('/download-product/{orderItem}', [App\Http\Controllers\OrderController::class, 'downloadDigitalProduct'])
        ->name('orders.download');

    // === MANAJEMEN PRODUK (CRUD) ===
    // Kita gunakan resource controller agar otomatis mencakup:
    // create, store, edit, update, destroy
    Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['index', 'show']);

    // === MANAJEMEN PESANAN (SELLER) ===
    // 1. List Pesanan Masuk
    Route::get('/shop/orders', [ShopController::class, 'orders'])->name('shop.orders');
    
    // 2. Detail Pesanan Seller
    Route::get('/shop/orders/{id}', [ShopController::class, 'showOrder'])->name('shop.orders.show');
    
    // 3. Update Status: Proses (Dikemas)
    Route::patch('/shop/orders/{id}/process', [ShopController::class, 'processOrder'])->name('shop.orders.process');
    
    // 4. Update Status: Kirim (Input Resi)
    Route::post('/shop/orders/{id}/ship', [ShopController::class, 'shipOrder'])->name('shop.orders.ship');
    
    // 5. Cetak Label Pengiriman
    Route::get('/shop/orders/{id}/label', [ShopController::class, 'printLabel'])->name('shop.orders.label');

    // === PROFILE ROUTES ===
// 1. Lihat Profil (Read Only)
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');


// 2. Edit Akun (Nama, Email, HP User)
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

// 3. Edit Alamat (Halaman Terpisah)
Route::get('/profile/address', [ProfileController::class, 'editAddress'])->name('profile.address_edit');
// Route::put('/profile/address', [ProfileController::class, 'updateAddress'])->name('profile.address.update');

    // === SELLER / TOKO (Management) ===
    // Dashboard Toko
    Route::get('/my-shop', [ShopController::class, 'index'])->name('shop.index');
    
    // Buka Toko Baru
    Route::get('/open-shop', [ShopController::class, 'create'])->name('shop.create');
    Route::post('/open-shop', [ShopController::class, 'store'])->name('shop.store');

    // Edit Profil Toko
    Route::get('/shop/edit', [ShopController::class, 'edit'])->name('shop.edit');
    Route::put('/shop/update', [ShopController::class, 'update'])->name('shop.update');

    // Pesanan Masuk (Seller Management)
    Route::get('/shop/orders', function() {
        return "Halaman Kelola Pesanan Masuk (Coming Soon)";
    })->name('shop.orders');

    // Keuangan / Penarikan Dana
    Route::get('/shop/finance', function() {
        return "Halaman Request Pencairan Dana (Coming Soon)";
    })->name('shop.finance');


    // === BUYER / TRANSAKSI ===
    // Dashboard Utama User (Riwayat Pesanan)
    Route::get('/dashboard', [OrderController::class, 'history'])->name('dashboard');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Tambahkan ini di bawah route cart.store
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');

    // Checkout
    Route::get('/checkout', [OrderController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');

    // === ORDER DETAILS & PAYMENT ===
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/order/{id}/pay', [OrderController::class, 'markAsPaid'])->name('orders.pay');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Verifikasi Toko
    Route::patch('/shop/{id}/approve', [AdminController::class, 'approveShop'])->name('admin.shop.approve');
    Route::delete('/shop/{id}/reject', [AdminController::class, 'rejectShop'])->name('admin.shop.reject');
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Bisa diakses semua orang)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [FrontController::class, 'index'])->name('front.index');

// Detail Produk
Route::get('/product/{slug}', [FrontController::class, 'show'])->name('front.product');
