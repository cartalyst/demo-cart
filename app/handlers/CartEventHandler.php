<?php namespace App\Handlers;

use App\Models\Product;
use Cartalyst\Cart\Cart;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Events\Dispatcher;
use Cartalyst\Cart\Collections\ItemCollection;

class CartEventHandler {

	/**
	 * The logged in user instance.
	 *
	 * @var \Cartalyst\Sentinel\Sentinel\Users\EloquentUser
	 */
	protected $user = null;

	/**
	 * Constructor.
	 *
	 * @param  \Cartalyst\Sentinel\Sentinel  $sentinel
	 * @return void
	 */
	public function __construct(Sentinel $sentinel)
	{
		$this->user = $sentinel->getUser();
	}

	/**
	 * Listen to the events.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('cartalyst.cart.added', __CLASS__.'@onItemAdded');

		$dispatcher->listen('cartalyst.cart.updated', __CLASS__.'@onItemUpdated');

		$dispatcher->listen('cartalyst.cart.removed', __CLASS__.'@onItemRemoved');

		$dispatcher->listen('cartalyst.cart.cleared', __CLASS__.'@onCartCleared');
	}

	/**
	 * When an item is added to the cart.
	 *
	 * @param  \Cartalyst\Cart\Collections\ItemCollection  $item
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @return void
	 */
	public function onItemAdded(ItemCollection $item, Cart $cart)
	{
		// Check if the user is logged in
		if ( ! $this->user) return;

		// Store the item on the database
		$this->storeItem($item, $cart);
	}

	/**
	 * When an item from the cart is updated.
	 *
	 * @param  \Cartalyst\Cart\Collections\ItemCollection  $item
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @return void
	 */
	public function onItemUpdated(ItemCollection $item, Cart $cart)
	{
		// Check if the user is logged in
		if ( ! $this->user) return;

		// Update the item on the database
		$this->storeItem($item, $cart);
	}

	/**
	 * When an item is removed from the cart.
	 *
	 * @param  \Cartalyst\Cart\Collections\ItemCollection  $item
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @return void
	 */
	public function onItemRemoved(ItemCollection $item, Cart $cart)
	{
		// Check if the user is logged in
		if ( ! $this->user) return;

		// Get the product that was added to the shopping cart
		$product = Product::find($item->get('id'));

		// Remove the product from the database
		$this->cart($cart->getIdentity())->items()->whereProductId($product->id)->delete();
	}

	/**
	 * When the cart is cleared.
	 *
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @return void
	 */
	public function onCartCleared(Cart $cart)
	{
		// Check if the user is logged in
		if ( ! $this->user) return;

		// Remove all the items from the database
		$this->cart($cart->getIdentity())->items()->delete();
	}

	/**
	 * Returns the user cart associated to the given cart instance.
	 *
	 * @param  string  $instance
	 * @return \App\Models\Cart
	 */
	protected function cart($instance)
	{
		if ( ! $cart = $this->user->cart()->whereInstance($instance)->first())
		{
			$cart = $this->user->cart()->create(compact('instance'));
		}

		return $cart;
	}

	/**
	 * Store or update the item on local storage.
	 *
	 * @param  \Cartalyst\Cart\Collections\ItemCollection  $item
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @return \App\Models\CartItem
	 */
	protected function storeItem(ItemCollection $item, Cart $cart)
	{
		// Get the product that was added to the shopping cart
		$product = Product::find($item->get('id'));

		// Get the cart from storage that belongs to the instance
		$_cart = $this->cart($cart->getIdentity());

		// Does the product exist on storage?
		if ( ! $_item = $_cart->items()->whereProductId($product->id)->first())
		{
			$_item = $_cart->items()->create([
				'product_id' => $product->id,
				'quantity'   => $item->get('quantity'),
			]);
		}
		else
		{
			$_item->update([
				'quantity' => $item->get('quantity')
			]);
		}

		return $_item;
	}

}
