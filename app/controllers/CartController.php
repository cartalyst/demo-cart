<?php

use App\Models\Product;
use Cartalyst\Conditions\Condition;
use Illuminate\Support\SerializableClosure;

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
		$product = Product::find($id);

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
			'id'         => $product->id,
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
		$cart = app('cart');

		$code = Input::get('coupon');

		$coupons = [
			'PROMO14' => [
				'data' => [
					'name'   => "Limited Time 10% Off (code: $code)",
					'type'   => 'coupon',
					'target' => 'subtotal',
				],
				'actions' => [
					'value' => '-10%',
				],
				'rules' => [],
			],
			'DISC2014' => [
				'data' => [
					'name'   => "Limited Time $25 Off on all purchases over $200 (code: $code)",
					'type'   => 'coupon',
					'target' => 'subtotal',
				],
				'actions' => [
					'value' => '-25',
				],
				'rules' => new SerializableClosure(function()
				{
					return Cart::subtotal() > 200;
				}),
			],
		];

		if ( ! $coupon = array_get($coupons, $code))
		{
			return Redirect::back()->withErrors("[$code] is not a valid code.");
		}

		$couponCondition = new Condition($coupon['data']);

		$couponCondition->setRules($coupon['rules']);
		$couponCondition->setActions($coupon['actions']);

		$cart->condition($couponCondition);

		return Redirect::back()->withSuccess("Coupon has been applied.");
	}

	public function removeCoupon($name)
	{
		$cart = app('cart');

		$cart->removeCondition($name);

		return Redirect::back();
	}

}
