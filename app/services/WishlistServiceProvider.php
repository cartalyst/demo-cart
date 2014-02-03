<?php

use Cartalyst\Cart\Cart;
use Cartalyst\Cart\Storage\Sessions\IlluminateSession;
use Illuminate\Support\ServiceProvider;

class WishlistServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSession();

        $this->registerCart();
    }

    /**
     * Register the session driver used by the Wishlist.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->app['wishlist.storage'] = $this->app->share(function($app)
        {
            $config = $app['config']->get('cartalyst/cart::config');

            return new IlluminateSession($app['session.store'], $config['session_key'], 'wishlist');
        });
    }

    /**
     * Register the Wishlist.
     *
     * @return void
     */
    protected function registerCart()
    {
        $this->app['wishlist'] = $this->app->share(function($app)
        {
            return new Cart('wishlist', $app['wishlist.storage'], $app['events']);
        });
    }

}
