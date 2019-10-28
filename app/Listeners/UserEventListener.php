<?php

namespace App\Listeners;

use Cartalyst\Cart\Cart;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Events\Dispatcher;
use Cartalyst\Collections\Collection;
use Cartalyst\Sentinel\Users\EloquentUser;

class UserEventListener
{
    /**
     * The Cart instance.
     *
     * @var \Cartalyst\Cart\Cart
     */
    protected $cart;

    /**
     * The Wishlist instance.
     *
     * @var \Cartalyst\Cart\Cart
     */
    protected $wishlist;

    /**
     * The logged in user instance.
     *
     * @var \Cartalyst\Sentinel\Sentinel\Users\EloquentUser
     */
    protected $user = null;

    /**
     * Constructor.
     *
     * @param \Cartalyst\Cart\Cart         $cart
     * @param \Cartalyst\Sentinel\Sentinel $sentinel
     *
     * @return void
     */
    public function __construct(Cart $cart, Sentinel $sentinel)
    {
        $this->cart = $cart;

        $this->wishlist = app('wishlist');

        $this->user = $sentinel->getUser();
    }

    /**
     * Listen to the events.
     *
     * @param \Illuminate\Events\Dispatcher $dispatcher
     *
     * @return void
     */
    public function subscribe(Dispatcher $dispatcher): void
    {
        $dispatcher->listen('sentinel.authenticated', __CLASS__ . '@onUserAuthenticated');
    }

    /**
     * When a user logs in.
     *
     * @param \Cartalyst\Sentinel\Users\EloquentUser $user
     *
     * @return void
     */
    public function onUserAuthenticated(EloquentUser $user): void
    {
        $this->syncFromDatabase();

        $this->syncToDatabase();
    }

    /**
     * Syncs the items we have on the database to the cart.
     *
     * @return void
     */
    protected function syncFromDatabase(): void
    {
        $userCart = $this->user->carts()->whereInstance('main')->first();

        $userWhishlist = $this->user->carts()->whereInstance('wishlist')->first();

        // Add the cart items
        if ($userCart) {
            $this->addItems($userCart, $this->cart);
        }

        // Add the wishlist items
        if ($userWhishlist) {
            $this->addItems($userWhishlist, $this->wishlist);
        }
    }

    /**
     * Syncs the items we have on the cart to the database.
     *
     * @return void
     */
    protected function syncToDatabase(): void
    {
        // Get the user carts relationship
        $userCarts = $this->user->carts();

        // Make sure that the cart instance is stored
        $instance = $this->cart->getInstance();

        //
        if (! $cart = $userCarts->whereInstance($instance)->first()) {
            $cart = $userCarts->create(compact('instance'));
        }

        foreach ($this->cart->items() as $item) {
            $id = $item->get('id');

            $payload = [
                'product_id' => $id,
                'quantity'   => $item->get('quantity'),
            ];

            if (! $_item = $cart->items()->where('product_id', $id)->first()) {
                $cart->items()->create($payload);
            } else {
                $_item->update($payload);
            }
        }
    }

    protected function addItems($cart, Cart $instance)
    {
        $items = [];

        foreach ($cart->items as $item) {
            $id = $item->product->id;

            $_item = $instance->find(compact('id'));

            if (count($_item) === 0) {
                $items[] = [
                    'id'       => $id,
                    'name'     => $item->product->name,
                    'price'    => $item->product->price,
                    'quantity' => $item->quantity,
                ];
            }
        }

        $instance->sync(new Collection($items));
    }
}
