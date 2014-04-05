<?php

use App\Models\Cart as CartModel;
use App\Models\Product;
use Illuminate\Support\Collection;

Event::listen('sentry.authenticated', function($user)
{
	$items = array();

	foreach ($user->cart as $cart)
	{
		foreach ($cart->items as $item)
		{
			$slug = $item->product->slug;

			$search = Cart::find(array('id' => $slug));

			if (count($search) === 0)
			{
				$items[$cart->instance][] = array(
					'id'       => $slug,
					'name'     => $item->product->name,
					'price'    => $item->product->price,
					'quantity' => 1,
				);
			}
		}
	}

	// Sync the main Cart
	app('cart')->sync(new Collection(array_get($items, 'main', array())));

	// Sync the wishlist
	app('wishlist')->sync(new Collection(array_get($items, 'wishlist', array())));
});

# this check can and should be done on each event listener
if (Sentry::check())
{

	Event::listen('cartalyst.cart.added', function($item, $cart)
	{
		$product = Product::where('slug', $item->get('id'))->first();

		if ( ! $cart = Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first())
		{
			$cart = Sentry::getUser()->cart()->create(compact('instance'));
		}

		$cart->items()->create(array(
			'product_id' => $product->id,
			'quantity'   => $item->get('quantity'),
		));
	});

	Event::listen('cartalyst.cart.updated', function($item, $cart)
	{
		$product = Product::where('slug', $item->get('id'))->first();

		$cart = Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first();

		$cart->items()->where('product_id', $product->id)->update(array(
			'quantity' => $item->get('quantity')
		));
	});

	Event::listen('cartalyst.cart.removed', function($item, $cart)
	{
		$product = Product::where('slug', $item->get('id'))->first();

		$cart = Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first();

		$cart->items()->where('product_id', $product->id)->delete();
	});

	Event::listen('cartalyst.cart.cleared', function($cart)
	{
		Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first()->items()->delete();
	});

}
