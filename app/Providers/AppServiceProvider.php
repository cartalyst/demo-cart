<?php

namespace App\Providers;

use Cartalyst\Cart\Cart;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Cart\Storage\IlluminateSession;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('wishlist', function ($app) {
            $config = $app['config']->get('cartalyst/cart::config');

            $storage = new IlluminateSession($app['session.store'], 'wishlist', $config['session_key']);

            return new Cart($storage, $app['events']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set the global and items Conditions order
        app('cart')->setConditionsOrder(['discount', 'other', 'tax', 'shipping', 'coupon']);
        app('cart')->setItemsConditionsOrder(['discount', 'other', 'tax', 'shipping']);

        // Apply default Conditions
        $condition1 = createCondition('VAT (17.5%)', 'tax', 'subtotal', ['value' => '17.50%']);

        $condition2 = createCondition('VAT (23%)', 'tax', 'subtotal', ['value' => '23%']);

        $condition3 = createCondition('Discount (7.5%)', 'discount', 'subtotal', ['value' => '-7.5%']);

        app('cart')->condition([$condition1, $condition2, $condition3]);
    }
}
