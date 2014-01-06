<?php

use Cartalyst\Conditions\Condition;

class CartController extends BaseController {

	public function index()
	{
		$cart = Cart::instance('main');

		$items = $cart->items();

		$total = $cart->total();

		return View::make('cart.cart', compact('cart', 'items', 'total'));
	}

	public function add($id)
	{
		$product = Product::where('slug', $id)->first();

		$condition1 = new Condition(array(
			'name'   => 'VAT (17.5%)',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition1->setActions(array(
			array('value' => '17.50%'),
			array('value' => '5%'),
		));

		$condition2 = new Condition(array(
			'name'   => 'VAT (23%)',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition2->setActions(array(
			'value' => '23%',
		));

		$data = array(
			'id'         => $product->slug,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
			'conditions' => array($condition1, $condition2),
		);

		Cart::instance('main')->add($data);

		return Redirect::to('cart');
	}

	public function update()
	{
		Cart::update(Input::get('update'));

		return Redirect::to('cart');
	}

	public function delete($id)
	{
		Cart::instance('main')->remove($id);

		return Redirect::to('cart');
	}

	public function destroy()
	{
		Cart::instance('main')->clear();

		return Redirect::to('cart');
	}

}
