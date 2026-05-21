<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromoCodeController as AdminPromoCodeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SteamWalletController;
use App\Http\Controllers\SupportChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/offer', [HomeController::class, 'offer'])->name('offer');
Route::get('/delivery', [ServiceController::class, 'delivery'])->name('services.delivery');
Route::get('/guarantee', [ServiceController::class, 'guarantee'])->name('services.guarantee');
Route::get('/steam-wallet', [SteamWalletController::class, 'show'])->name('services.steam-wallet');
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::post('/support/chat', [SupportChatController::class, 'chat'])
    ->middleware('throttle:20,1')
    ->name('support.chat');

Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/promo', [CartController::class, 'applyPromo'])->name('cart.promo.apply');
    Route::post('/cart/promo/remove', [CartController::class, 'removePromo'])->name('cart.promo.remove');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
    Route::post('/cart/checkout', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{order}/payment', [OrderController::class, 'payment'])->name('order.payment');
    Route::post('/order/{order}/payment', [OrderController::class, 'processPayment'])->name('order.payment.process');

    Route::post('/steam-wallet', [SteamWalletController::class, 'store'])->name('services.steam-wallet.store');
    Route::get('/steam-wallet/pay/{topup}', [SteamWalletController::class, 'payment'])->name('services.steam-wallet.payment');
    Route::post('/steam-wallet/pay/{topup}', [SteamWalletController::class, 'processPayment'])->name('services.steam-wallet.payment.process');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/products/{product}/image', [AdminProductController::class, 'storeImage'])->name('products.image.store');
    Route::delete('/products/{product}/image', [AdminProductController::class, 'destroyImage'])->name('products.image.destroy');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/promo-codes', [AdminPromoCodeController::class, 'index'])->name('promo-codes.index');
    Route::post('/promo-codes', [AdminPromoCodeController::class, 'store'])->name('promo-codes.store');
    Route::put('/promo-codes/{promoCode}', [AdminPromoCodeController::class, 'update'])->name('promo-codes.update');
    Route::delete('/promo-codes/{promoCode}', [AdminPromoCodeController::class, 'destroy'])->name('promo-codes.destroy');

    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/{message}/toggle', [AdminContactController::class, 'toggle'])->name('contacts.toggle');
});
