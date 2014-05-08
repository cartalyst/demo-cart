<?php

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'cart'], function()
{
	Route::get('/'          , 'CartController@index');
	Route::post('/'         , 'CartController@update');
	Route::get('{id}/add'   , 'CartController@add');
	Route::get('{id}/remove', 'CartController@delete');
	Route::get('destroy'    , 'CartController@destroy');
});

Route::group(['prefix' => 'wishlist'], function()
{
	Route::get('/'          , 'WishlistController@index');
	Route::post('/'         , 'WishlistController@update');
	Route::get('{id}/add'   , 'WishlistController@add');
	Route::get('{id}/remove', 'WishlistController@delete');
	Route::get('destroy'    , 'WishlistController@destroy');
});

Route::group(['prefix' => 'coupon'], function()
{
	Route::post('/', ['as' => 'applyCoupon', 'uses' => 'CartController@applyCoupon']);
	Route::get('remove/{name}', 'CartController@removeCoupon');
});

Route::get('login', function()
{
	return View::make('cart.login');
});

Route::post('login', function()
{
	if (Sentry::authenticate(Input::all()))
	{
		return Redirect::to('/');
	}

	return Redirect::to('login');
});

Route::get('logout', function()
{
	Sentry::logout();

	return Redirect::to('/');
});
