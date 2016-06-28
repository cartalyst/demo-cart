<?php

namespace App\Providers;

use Cartalyst\Cart\Cart;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Cart\Storage\IlluminateSession;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('wishlist', function($app) {
        	$config = $app['config']->get('cartalyst/cart::config');

        	$storage = new IlluminateSession($app['session.store'], 'wishlist', $config['session_key']);

        	return new Cart($storage, $app['events']);
        });
    }
}
