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
			$id = $item->product->id;

			$search = Cart::find(array('id' => $id));

			if (count($search) === 0)
			{
				$items[$cart->instance][] = array(
					'id'       => $id,
					'name'     => $item->product->name,
					'price'    => $item->product->price,
					'quantity' => 1,
				);
			}
		}
	}

	$instance = Cart::getIdentity();

	if ( ! $cart = Sentry::getUser()->cart()->where('instance', $instance)->first())
	{
		$cart = Sentry::getUser()->cart()->create(compact('instance'));
	}

	foreach (Cart::items() as $item)
	{
		$id = $item->get('id');

		if ( ! $cart->items()->where('product_id', $id)->first())
		{
			$cart->items()->create(array(
				'product_id' => $id,
				'quantity'   => $item->get('quantity'),
			));
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
		$product  = Product::find($item->get('id'));
		$instance = $cart->getIdentity();

		if ( ! $cart = Sentry::getUser()->cart()->where('instance', $instance)->first())
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
		$product = Product::find($item->get('id'));

		$cart = Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first();

		$cart->items()->where('product_id', $product->id)->update(array(
			'quantity' => $item->get('quantity')
		));
	});

	Event::listen('cartalyst.cart.removed', function($item, $cart)
	{
		$product = Product::find($item->get('id'));

		$cart = Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first();

		if ($cart)
		{
			$cart->items()->where('product_id', $product->id)->delete();
		}
	});

	Event::listen('cartalyst.cart.cleared', function($cart)
	{
		Sentry::getUser()->cart()->where('instance', $cart->getIdentity())->first()->items()->delete();
	});

}
