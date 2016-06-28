<?php

use Illuminate\Routing\Router;

$router->get('/', 'HomeController@index');

$router->group([ 'prefix' => 'cart' ], function(Router $router) {
	$router->get('/', 'CartController@index')->name('cart');
	$router->post('/', 'CartController@update')->name('cart.update');
    $router->get('count', 'CartController@count')->name('cart.count');
    $router->get('destroy', 'CartController@destroy')->name('cart.destroy');

    $router->group([ 'prefix' => '{id}' ], function(Router $router) {
    	$router->get('add', 'CartController@add')->name('cart.add-item');
        $router->get('move', 'CartController@move')->name('cart.move-item');
    	$router->get('remove', 'CartController@delete')->name('cart.remove-item');
    });
});
$router->group([ 'prefix' => 'wishlist' ], function(Router $router) {
	$router->get('/', 'WishlistController@index')->name('wishlist');
	$router->post('/', 'WishlistController@update')->name('wishlist.update');
    $router->get('count', 'WishlistController@count')->name('wishlist.count');
    $router->get('destroy', 'WishlistController@destroy')->name('wishlist.destroy');

    $router->group([ 'prefix' => '{id}' ], function(Router $router) {
    	$router->get('add', 'WishlistController@add')->name('wishlist.add-item');
        $router->get('move', 'WishlistController@move')->name('wishlist.move-item');
    	$router->get('remove', 'WishlistController@delete')->name('wishlist.remove-item');
    });
});

$router->group(['prefix' => 'coupon'], function(Router $router) {
	$router->post('/', 'CartController@applyCoupon')->name('coupon.apply');
	$router->get('remove/{name}', 'CartController@removeCoupon')->name('coupon.remove');
});

$router->get('login', function() {
	return View::make('cart.login');
});

$router->post('login', function() {
	if (Sentinel::authenticate(Input::all())) {
		return Redirect::to('/');
	}

	return Redirect::to('login');
});

$router->get('logout', function() {
	Sentinel::logout();

	return Redirect::to('/');
});
