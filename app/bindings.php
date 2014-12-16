<?php

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\IlluminateSession;

/*
|--------------------------------------------------------------------------
| Wishlist Binding
|--------------------------------------------------------------------------
|
| We'll bind our wishlist into the container.
|
*/

$app['wishlist'] = $app->share(function($app)
{
	$config = $app['config']->get('cartalyst/cart::config');

	$storage = new IlluminateSession($app['session.store'], 'wishlist', $config['session_key']);

	return new Cart($storage, $app['events']);
});
