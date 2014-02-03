<?php

use Cartalyst\Conditions\Condition;

class CartController extends BaseController {

	public function index()
	{
		$cart = app('cart');

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
		));

		$condition2 = new Condition(array(
			'name'   => 'VAT (23%)',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition2->setActions(array(
			array('value' => '23%'),
		));

		$condition3 = new Condition(array(
			'name'   => 'Discount (7.5%)',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$condition3->setActions(array(
			array('value' => '-7.5%'),
		));

		$data = array(
			'id'         => $product->slug,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
			'conditions' => array($condition1, $condition2, $condition3),
		);

		Cart::add($data);

		$condition1 = new Condition(array(
			'name'   => 'Global Tax (12.5%)',
			'type'   => 'tax',
			'target' => 'subtotal',
		));

		$condition1->setActions(array(
			array('value' => '12.50%'),
		));

		$condition2 = new Condition(array(
			'name'   => 'Global Discount (5%)',
			'type'   => 'discount',
			'target' => 'subtotal',
		));

		$condition2->setActions(array(
			array('value' => '-5%'),
		));

		$shippingCondition = new Condition(array(
			'name'   => 'Shipping',
			'type'   => 'shipping',
			'target' => 'subtotal',
		));

		$shippingCondition->setActions(array(
			array('value' => '20.00'),
		));

		Cart::condition(array($condition1, $condition2, $shippingCondition));

		Cart::setConditionsOrder(array(
			'discount',
			'other',
			'tax',
			'shipping',
		));

		return Redirect::to('cart');
	}

	public function update()
	{
		Cart::update(Input::get('update'));

		return Redirect::to('cart');
	}

	public function delete($id)
	{
		Cart::remove($id);

		return Redirect::to('cart');
	}

	public function destroy()
	{
		Cart::clear();

		return Redirect::to('cart');
	}

}
