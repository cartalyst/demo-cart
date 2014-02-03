<?php

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\Sessions\IlluminateSession;

$app = app();

$app['wishlist'] = $app->share(function($app)
{
	$config = $app['config']->get('cartalyst/cart::config');

	$storage = new IlluminateSession($app['session.store'], $config['session_key'], 'wishlist');

	return new Cart('wishlist', $storage, $app['events']);
});
