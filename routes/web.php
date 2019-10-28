<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Demo\CartController;
use App\Http\Controllers\Demo\HomeController;
use App\Http\Controllers\Demo\WishlistController;

Route::get('/', function () {
    return redirect('/demo');
    return view('welcome');
});

Route::prefix('/demo')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('demo.home');

    Route::get('/cart', [CartController::class, 'index'])->name('demo.cart');
    Route::post('/cart', [CartController::class, 'update'])->name('demo.cart.update');
    Route::get('/cart/count', [CartController::class, 'count'])->name('demo.cart.count');
    Route::get('/cart/destroy', [CartController::class, 'destroy'])->name('demo.cart.destroy');
    Route::get('/cart/{id}/add', [CartController::class, 'add'])->name('demo.cart.add');
    Route::get('/cart/{id}/move', [CartController::class, 'move'])->name('demo.cart.move');
    Route::get('/cart/{id}/remove', [CartController::class, 'delete'])->name('demo.cart.remove');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('demo.wishlist');
    Route::post('/wishlist', [WishlistController::class, 'update'])->name('demo.wishlist.update');
    Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('demo.wishlist.count');
    Route::get('/wishlist/destroy', [WishlistController::class, 'destroy'])->name('demo.wishlist.destroy');

    Route::get('/wishlist/{id}/add', [WishlistController::class, 'add'])->name('demo.wishlist.add');
    Route::get('/wishlist/{id}/move', [WishlistController::class, 'move'])->name('demo.wishlist.move');
    Route::get('/wishlist/{id}/remove', [WishlistController::class, 'delete'])->name('demo.wishlist.remove');

    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('demo.coupon.apply');
    Route::get('/coupon/remove/{name}', [CartController::class, 'removeCoupon'])->name('demo.coupon.remove');

    Route::get('login', function() {
        return view('demo.login');
    })->name('demo.login');

    Route::post('login', function() {
        $credentials = request()->only(['email', 'password']);

        if (Sentinel::authenticate($credentials)) {
            return redirect()->route('demo.home');
        }

        return redirect()->route('demo.login');
    });

    Route::get('logout', function() {
        Sentinel::logout();

        return redirect()->route('demo.home');
    })->name('demo.logout');
});
