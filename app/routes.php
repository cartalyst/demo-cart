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

Route::get('wishlist', 'WishlistController@index');
Route::get('wishlist/{id}/add', 'WishlistController@add');
Route::get('wishlist/{id}/remove', 'WishlistController@delete');
Route::get('wishlist/destroy', 'WishlistController@destroy');



