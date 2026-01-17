<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WithdrawalController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Bisa diakses siapa saja)
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/product/{slug}', [FrontController::class, 'show'])->name('front.product');
Route::get('/shop/{id}', [ShopController::class, 'show'])->name('shop.show'); 

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // === 1. FITUR UMUM ===
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // === 2. PROFIL USER ===
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Manajemen Alamat
    Route::get('/profile/address/create', [ProfileController::class, 'createAddress'])->name('profile.address.create');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'destroyAddress'])->name('profile.address.destroy');

    // === 3. BUYER AREA (PEMBELI) ===
    Route::get('/dashboard', [OrderController::class, 'history'])->name('dashboard');
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Checkout
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [OrderController::class, 'store'])->name('checkout.store');

    // Order Actions (Buyer)
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/order/{order}/pay', [OrderController::class, 'markAsPaid'])->name('orders.pay');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/download-product/{orderItem}', [OrderController::class, 'downloadDigitalProduct'])->name('orders.download');
    Route::patch('/order/{id}/complete', [OrderController::class, 'markAsCompleted'])->name('orders.complete');
    Route::patch('/orders/{order}/confirm-received', [OrderController::class, 'confirmReceived'])->name('orders.confirm-received');

    // Download Produk Digital
    Route::get('/orders/download/{orderItemId}', [App\Http\Controllers\OrderController::class, 'downloadDigitalProduct'])->name('orders.download')->middleware('auth');

    // Review
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// === 4. SELLER AREA (PENJUAL) ===
    
    // Dashboard & Manajemen Toko
    Route::get('/my-shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/open-shop', [ShopController::class, 'create'])->name('shop.create');
    Route::post('/open-shop', [ShopController::class, 'store'])->name('shop.store');

    // Edit & Update Toko
    Route::get('/my-shop/edit', [ShopController::class, 'edit'])->name('shop.edit');
    Route::put('/my-shop/update', [ShopController::class, 'update'])->name('shop.update');
    // -------------------------------------------------------------------

    // Manajemen Pesanan (Seller)
    Route::get('/my-shop/orders', [ShopController::class, 'orders'])->name('shop.orders'); 
    Route::get('/my-shop/orders/{id}', [ShopController::class, 'showOrder'])->name('shop.order.show'); 
    Route::post('/my-shop/orders/{id}/process', [ShopController::class, 'processOrder'])->name('shop.order.process'); 
    Route::post('/my-shop/orders/{id}/ship', [ShopController::class, 'shipOrder'])->name('shop.order.ship'); 
    Route::get('/my-shop/orders/{id}/label', [ShopController::class, 'printLabel'])->name('shop.order.label'); 

    // Manajemen Produk (Seller)
    Route::resource('products', ProductController::class)->except(['index', 'show']);
    Route::get('/seller/products', [ProductController::class, 'index'])->name('products.index');

    // Keuangan & Withdraw
    Route::get('/my-shop/finance', [ShopController::class, 'finance'])->name('shop.finance');
    Route::get('/my-shop/withdraw', [WithdrawalController::class, 'create'])->name('shop.withdraw.create');
    Route::post('/my-shop/withdraw', [WithdrawalController::class, 'store'])->name('shop.withdraw.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::patch('/shop/{id}/approve', [AdminController::class, 'approveShop'])->name('shop.approve');
    Route::delete('/shop/{id}/reject', [AdminController::class, 'rejectShop'])->name('shop.reject');
    Route::patch('/payment/{id}/confirm', [AdminController::class, 'confirmPayment'])->name('payment.confirm');

    // MANAJEMEN PENARIKAN DANA
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::patch('/withdrawals/{id}/approve', [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::delete('/withdrawals/{id}/reject', [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');

    // MANAJEMEN USERS
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // MANAJEMEN TOKO
    Route::get('/shops', [AdminController::class, 'shops'])->name('shops');
    Route::delete('/shops/{id}', [AdminController::class, 'deleteShop'])->name('shops.delete');

    Route::patch('/admin/payment/{id}/confirm', [AdminController::class, 'confirmPayment'])
      ->name('admin.payment.confirm');
});