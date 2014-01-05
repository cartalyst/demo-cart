<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');

Route::get('cart', 'CartController@index');
Route::post('cart', 'CartController@update');
Route::get('cart/{id}/remove', 'CartController@delete');
Route::get('cart/clear', 'CartController@clear');

Route::get('product/{id}', 'HomeController@product');
Route::get('cart/{id}/add', 'CartController@add');

Route::get('cart/remove/{id}', function($id)
{
	Cart::remove($id);

	return Redirect::to('cart');
});

Route::get('cart/destroy', function()
{
	Cart::destroy();

	return Redirect::to('cart');
});

Route::post('cart', function()
{
	Cart::update(Input::get('update'));

	return Redirect::to('cart');
});
