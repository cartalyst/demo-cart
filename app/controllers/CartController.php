<?php

use App\Models\Product;
use Cartalyst\Conditions\Condition;

class CartController extends BaseController {

	public function index()
	{
		$cart = app('cart');

		$items = $cart->items();

		$total = $cart->total();

		$conditions = $cart->conditions();

		$coupon = false;

		foreach ($cart->conditions() as $condition)
		{
			if ($condition->get('name') === 'Limited Time Offer (10% Off)')
			{
				$coupon = true;
			}
		}

		return View::make('cart.cart', compact('cart', 'items', 'total', 'coupon'));
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

		$shippingCondition = new Condition(array(
			'name'   => 'Item Based Shipping',
			'type'   => 'shipping',
			'target' => 'subtotal',
		));

		$shippingCondition->setActions(array(
			array('value' => '20.00'),
		));

		$data = array(
			'id'         => $product->slug,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
			'conditions' => array($condition1, $condition2, $condition3, $shippingCondition),
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
			'name'   => 'Global Shipping',
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
			'coupon',
		));

		Cart::setItemsConditionsOrder(array(
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

	public function applyCoupon()
	{
		$coupon = new Condition(array(
			'name'   => 'Limited Time Offer (10% Off)',
			'type'   => 'coupon',
			'target' => 'subtotal',
		));

		$coupon->setActions(array(
			array('value' => '-10.00%'),
		));

		$cart = app('cart');

		$cart->condition($coupon);

		return Redirect::back();
	}

	public function removeCoupon()
	{
		$cart = app('cart');

		$cart->clearConditions('coupon');

		return Redirect::back();
	}

}
