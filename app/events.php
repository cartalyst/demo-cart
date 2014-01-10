<?php

# this check can and should be done on each event listener
if (Sentry::check())
{

	Event::listen('cart.instance.created', function($instance)
	{
		Sentry::getUser()->cart()->create(compact('instance'));
	});

	Event::listen('cart.instance.removed', function($instance)
	{
		Sentry::getUser()->cart()->where('instance', $instance)->delete();
	});

	Event::listen('cart.added', function($item, $instance)
	{
		$product = Product::where('slug', $item->get('id'))->first();

		if ( ! $cart = Sentry::getUser()->cart()->where('instance', $instance)->first())
		{
			$cart = Sentry::getUser()->cart()->create(compact('instance'));
		}

		$cart->items()->create(array(
			'product_id' => $product->id,
			'quantity'   => $item->get('quantity'),
		));
	});

	Event::listen('cart.updated', function($item, $instance)
	{
		$product = Product::where('slug', $item->get('id'))->first();

		$cart = Sentry::getUser()->cart()->where('instance', $instance)->first();

		$cart->items()->where('product_id', $product->id)->update(array(
			'quantity' => $item->get('quantity')
		));
	});

	Event::listen('cart.removed', function($item, $instance)
	{
		$product = Product::where('slug', $item->get('id'))->first();

		$cart = Sentry::getUser()->cart()->where('instance', $instance)->first();

		$cart->items()->where('product_id', $product->id)->delete();
	});

	Event::listen('cart.cleared', function($instance)
	{
		Sentry::getUser()->cart()->where('instance', $instance)->first()->items()->delete();
	});

}
