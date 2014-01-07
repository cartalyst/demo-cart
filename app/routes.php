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
Route::get('cart/{id}/add', 'CartController@add');
Route::get('cart/{id}/remove', 'CartController@delete');
Route::get('cart/destroy', 'CartController@destroy');

$instances = Cart::instances();

array_forget($instances, 'main');

View::share('instances', $instances);

foreach ($instances as $instance => $col)
{
	Route::get("{$instance}", 'CartInstanceController@index');
	Route::get("{$instance}/{id}/add", 'CartInstanceController@add');
	Route::get("{$instance}/{id}/remove", 'CartInstanceController@delete');
	Route::get("{$instance}/destroy", 'CartInstanceController@destroy');
}

Route::get('login', function()
{
	return View::make('cart.login');
});

Route::get('logout', function()
{
	Sentry::logout();

	return Redirect::to('/');
});

Route::post('login', function()
{
	if (Sentry::authenticate(Input::all()))
	{
		return Redirect::to('/');
	}

	return Redirect::to('login');
});
