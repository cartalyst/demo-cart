<?php

use Cartalyst\Conditions\Condition;

class CartController extends BaseController {

	public function index()
	{
		$cart = Cart::instance('main');

		$items = $cart->items();

		$total = $cart->total();

		return View::make('cart.index', compact('cart', 'items', 'total'));
	}

	public function add($id)
	{
		$product = Product::where('slug', $id)->first();

		$condition = new Condition(array(
			'name'   => 'VAT (17.5%)',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition->setActions(array(
			'value' => '17.50%',
		));

		$data = array(
			'id'         => $product->slug,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
			'conditions' => $condition,
		);

		$cart = Cart::add($data);

		return Redirect::to('cart');
	}

}
