<?php

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'cart'], function()
{
	Route::get('/'              , 'CartController@index');
	Route::post('/'             , 'CartController@update');
	Route::get('{id}/add'       , 'CartController@add');
	Route::get('{id}/move'      , 'CartController@move');
	Route::get('{id}/addAjax'   , 'CartController@addAjax');
	Route::get('{id}/remove'    , 'CartController@delete');
	Route::get('{id}/removeAjax', 'CartController@deleteAjax');
	Route::get('destroy'        , 'CartController@destroy');
	Route::get('count'          , 'CartController@countAjax');
});

Route::group(['prefix' => 'wishlist'], function()
{
	Route::get('/'              , 'WishlistController@index');
	Route::post('/'             , 'WishlistController@update');
	Route::get('{id}/add'       , 'WishlistController@add');
	Route::get('{id}/move'      , 'WishlistController@move');
	Route::get('{id}/addAjax'   , 'WishlistController@addAjax');
	Route::get('{id}/remove'    , 'WishlistController@delete');
	Route::get('{id}/removeAjax', 'WishlistController@deleteAjax');
	Route::get('destroy'        , 'WishlistController@destroy');
	Route::get('count'          , 'WishlistController@countAjax');
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
	if (Sentinel::authenticate(Input::all()))
	{
		return Redirect::to('/');
	}

	return Redirect::to('login');
});

Route::get('logout', function()
{
	Sentinel::logout();

	return Redirect::to('/');
});
