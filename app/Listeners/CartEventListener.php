<?php

namespace App\Listeners;

use App\Product;
use Cartalyst\Cart\Cart;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Events\Dispatcher;
use Cartalyst\Cart\Collections\CartCollection;
use Cartalyst\Cart\Collections\ItemCollection;

class CartEventListener
{
    /**
     * The logged in user instance.
     *
     * @var \Cartalyst\Sentinel\Sentinel\Users\EloquentUser
     */
    protected $user = null;

    /**
     * Constructor.
     *
     * @param \Cartalyst\Sentinel\Sentinel $sentinel
     *
     * @return void
     */
    public function __construct(Sentinel $sentinel)
    {
        $this->user = $sentinel->getUser();
    }

    /**
     * Listen to the events.
     *
     * @param \Illuminate\Events\Dispatcher $dispatcher
     *
     * @return void
     */
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen('cartalyst.cart.created', __CLASS__ . '@onCartCreated');

        $dispatcher->listen('cartalyst.cart.added', __CLASS__ . '@onItemAdded');

        $dispatcher->listen('cartalyst.cart.updated', __CLASS__ . '@onItemUpdated');

        $dispatcher->listen('cartalyst.cart.removed', __CLASS__ . '@onItemRemoved');

        $dispatcher->listen('cartalyst.cart.cleared', __CLASS__ . '@onCartCleared');
    }

    /**
     * When an cart is created.
     *
     * @param \Cartalyst\Cart\Collections\CartCollection $cart
     *
     * @return void
     */
    public function onCartCreated(CartCollection $cart)
    {
        // Global conditions
        $condition1 = createCondition('Global Tax (12.5%)', 'tax', 'subtotal', ['value' => '12.50%']);
        $condition2 = createCondition('Global discount (5%)', 'tax', 'subtotal', ['value' => '-5%']);
        $condition3 = createCondition('Global Shipping', 'shipping', 'subtotal', ['value' => '20.00%']);

        // Set the global conditions
        $cart->condition([$condition1, $condition2, $condition3]);
    }

    /**
     * When an item is added to the cart.
     *
     * @param \Cartalyst\Cart\Collections\ItemCollection $item
     * @param \Cartalyst\Cart\Cart                       $cart
     *
     * @return void
     */
    public function onItemAdded(ItemCollection $item, Cart $cart)
    {
        if ($this->user) {
            $this->storeItem($item, $cart);
        }
    }

    /**
     * When an item from the cart is updated.
     *
     * @param \Cartalyst\Cart\Collections\ItemCollection $item
     * @param \Cartalyst\Cart\Cart                       $cart
     *
     * @return void
     */
    public function onItemUpdated(ItemCollection $item, Cart $cart)
    {
        // Check if the user is logged in
        if ($this->user) {
            // Store the item on the database
            $this->storeItem($item, $cart);
        }
    }

    /**
     * When an item is removed from the cart.
     *
     * @param \Cartalyst\Cart\Collections\ItemCollection $item
     * @param \Cartalyst\Cart\Cart                       $cart
     *
     * @return void
     */
    public function onItemRemoved(ItemCollection $item, Cart $cart)
    {
        // Check if the user is logged in
        if ($this->user) {
            // Get the product that was added to the shopping cart
            $product = Product::find($item->get('id'));

            // Remove the product from the database
            $this->cart($cart->getInstance())->items()->whereProductId($product->id)->delete();
        }
    }

    /**
     * When the cart is cleared.
     *
     * @param \Cartalyst\Cart\Cart $cart
     *
     * @return void
     */
    public function onCartCleared(Cart $cart)
    {
        if ($this->user) {
            $this->cart($cart->getInstance())->items()->delete();
        }
    }

    /**
     * Returns the user cart associated to the given cart instance.
     *
     * @param string $instance
     *
     * @return \App\Models\Cart
     */
    protected function cart(string $instance)
    {
        $userCarts = $this->user->carts();

        if (!$cart = $userCarts->whereInstance($instance)->first()) {
            $cart = $userCarts->create(compact('instance'));
        }

        return $cart;
    }

    /**
     * Store or update the item on local storage.
     *
     * @param \Cartalyst\Cart\Collections\ItemCollection $item
     * @param \Cartalyst\Cart\Cart                       $cart
     *
     * @return \App\Models\CartItem
     */
    protected function storeItem(ItemCollection $item, Cart $cart)
    {
        // Get the product that was added to the shopping cart
        $product = Product::find($item->get('id'));

        // Get the cart from storage that belongs to the instance
        $_cart = $this->cart($cart->getInstance());

        // Does the product exist on storage?
        if (!$_item = $_cart->items()->whereProductId($product->id)->first()) {
            $_item = $_cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $item->get('quantity'),
            ]);
        } else {
            $_item->update([
                'quantity' => $item->get('quantity')
            ]);
        }

        return $_item;
    }
}
