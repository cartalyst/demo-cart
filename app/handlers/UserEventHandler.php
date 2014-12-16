<?php namespace App\Handlers;

use Cartalyst\Cart\Cart;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Cartalyst\Sentinel\Users\EloquentUser;

class UserEventHandler {

	/**
	 * The logged in user instance.
	 *
	 * @var \Cartalyst\Sentinel\Sentinel\Users\EloquentUser
	 */
	protected $user = null;

	/**
	 * Constructor.
	 *
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @param  \Cartalyst\Sentinel\Sentinel  $sentinel
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
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('sentinel.authenticated', __CLASS__.'@onUserAuthenticated');
	}

	/**
	 * When a user logs in.
	 *
	 * @param  \Cartalyst\Sentinel\Users\EloquentUser  $user
	 * @return void
	 */
	public function onUserAuthenticated(EloquentUser $user)
	{
		$this->syncFromDatabase();

		$this->syncToDatabase();
	}

	protected function cart()
	{
		// Make sure that the cart instance is stored
		$instance = $this->cart->getInstance();

		if ( ! $cart = $this->user->cart()->whereInstance($instance)->first())
		{
			$cart = $this->user->cart()->create(compact('instance'));
		}

		return $cart;
	}

	// Syncs the items we have on the database to the cart
	protected function syncFromDatabase()
	{
		$cart = $this->cart();

		$items = [];

		foreach ($this->user->cart as $cart)
		{
			foreach ($cart->items as $item)
			{
				$id = $item->product->id;

				$_item = $this->cart->find(compact('id'));

				if (count($_item) === 0)
				{
					$items[$cart->instance][] = [
						'id'       => $id,
						'name'     => $item->product->name,
						'price'    => $item->product->price,
						'quantity' => $item->quantity,
					];
				}
			}
		}

		// Sync the main Cart
		$this->cart->sync(new Collection(array_get($items, 'main', [])));

		// Sync the wishlist
		$this->wishlist->sync(new Collection(array_get($items, 'wishlist', [])));
	}

	// Syncs the items we have on the cart to the database
	protected function syncToDatabase()
	{
		$cart = $this->cart();

		foreach ($this->cart->items() as $item)
		{
			$id = $item->get('id');

			if ( ! $_item = $cart->items()->where('product_id', $id)->first())
			{
				$cart->items()->create([
					'product_id' => $id,
					'quantity'   => $item->get('quantity'),
				]);
			}
			else
			{
				$_item->update([
					'quantity' => $item->get('quantity'),
				]);
			}
		}
	}

}
