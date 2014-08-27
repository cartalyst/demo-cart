<?php namespace App\Handlers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;
use Cartalyst\Sentinel\Users\EloquentUser;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

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
	 * @return void
	 */
	public function __construct()
	{
		$this->user = Sentinel::getUser();
	}

	/**
	 * When a user logs in.
	 *
	 * @param  \Cartalyst\Sentinel\Users\EloquentUser  $user
	 * @return void
	 */
	public function onUserAuthenticated(EloquentUser $user)
	{
		// Get the main cart instance
		$cart = app('cart');

		// Get the wishlist cart instance
		$wishlist = app('wishlist');

		$items = [];

		foreach ($user->cart as $_cart)
		{
			foreach ($_cart->items as $item)
			{
				$id = $item->product->id;

				$search = $cart->find(compact('id'));

				if (count($search) === 0)
				{
					$items[$_cart->instance][] = [
						'id'       => $id,
						'name'     => $item->product->name,
						'price'    => $item->product->price,
						'quantity' => 1,
					];
				}
			}
		}

		$instance = $cart->getIdentity();

		if ( ! $_cart = $this->user->cart()->whereInstance($instance)->first())
		{
			$_cart = $this->user->cart()->create(compact('instance'));
		}

		foreach ($_cart->items() as $item)
		{
			$id = $item->get('id');

			if ( ! $_cart->items()->where('product_id', $id)->first())
			{
				$_cart->items()->create([
					'product_id' => $id,
					'quantity'   => $item->get('quantity'),
				]);
			}
		}

		// Sync the main Cart
		$cart->sync(new Collection(array_get($items, 'main', [])));

		// Sync the wishlist
		$wishlist->sync(new Collection(array_get($items, 'wishlist', [])));
	}

	/**
	 * Listen to the events.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('sentinel.authenticated', 'App\Handlers\UserEventHandler@onUserAuthenticated');
	}

}
