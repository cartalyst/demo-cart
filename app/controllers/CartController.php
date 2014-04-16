<?php

use App\Models\Product;
use Cartalyst\Conditions\Condition;
use Illuminate\Support\SerializableClosure;

class CartController extends BaseController {

	/**
	 * The main cart instance.
	 *
	 * @var \Cartalyst\Cart\Cart
	 */
	protected $cart;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->cart = app('cart');
	}

	public function index()
	{
		$cart = $this->cart;

		$items = $cart->items();

		$total = $cart->total();

		$coupon = $cart->conditions('coupon');

		return View::make('cart.cart', compact('cart', 'items', 'total', 'coupon'));
	}

	public function add($id)
	{
		// Get the product from the database
		if ( ! $product = Product::find($id))
		{
			return Redirect::to('/');
		}

		// Item conditions
		$condition1 = $this->createCondition('VAT (17.5%)', 'tax', 'subtotal', ['value' => '17.50%']);
		$condition2 = $this->createCondition('VAT (23%)', 'tax', 'subtotal', ['value' => '23%']);
		$condition3 = $this->createCondition('Discount (7.5%)', 'discount', 'subtotal', ['value' => '-7.5%']);
		$condition4 = $this->createCondition('Item Based Shipping', 'shipping', 'subtotal', ['value' => '20.00']);

		// Add the item to the cart
		$this->cart->add([
			'id'         => $product->id,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
			'conditions' => [$condition1, $condition2, $condition3, $condition4],
		]);

		// Global conditions
		$condition1 = $this->createCondition('Global Tax (12.5%)', 'tax', 'subtotal', ['value' => '12.50%']);
		$condition2 = $this->createCondition('Global discount (5%)', 'tax', 'subtotal', ['value' => '-5%']);
		$condition3 = $this->createCondition('Global Shipping', 'shipping', 'subtotal', ['value' => '20.00%']);

		// Set the global conditions
		$this->cart->condition([$condition1, $condition2, $condition3]);

		// Set the global conditions order
		$this->cart->setConditionsOrder([
			'discount',
			'other',
			'tax',
			'shipping',
			'coupon',
		]);

		// Set the items conditions order
		$this->cart->setItemsConditionsOrder([
			'discount',
			'other',
			'tax',
			'shipping',
		]);

		return Redirect::to('cart');
	}

	public function update()
	{
		$this->cart->update(Input::get('update'));

		return Redirect::to('cart');
	}

	public function delete($id)
	{
		$this->cart->remove($id);

		return Redirect::to('cart');
	}

	public function destroy()
	{
		$this->cart->clear();

		return Redirect::to('cart');
	}

	public function applyCoupon()
	{
		$code = Input::get('coupon');

		$coupons = [

			'PROMO14' => [
				'data' => [
					'code'   => 'PROMO14',
					'name'   => 'Limited Time 10% Off',
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
					'code'   => 'DISC2014',
					'name'   => 'Limited Time $25 Off on all purchases over $200',
					'type'   => 'coupon',
					'target' => 'subtotal',
				],
				'actions' => [
					'value' => '-25',
				],
				'rules' => new SerializableClosure(function()
				{
					return app('cart')->subtotal() > 200;
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

		$this->cart->condition($couponCondition);

		return Redirect::back()->withSuccess("Coupon has been applied.");
	}

	public function removeCoupon($name)
	{
		$this->cart->removeCondition($name);

		return Redirect::back();
	}

	protected function createCondition($name, $type, $target, $actions = [], $rules = [])
	{
		$condition = new Condition(compact('name', 'type', 'target'));

		$condition->setActions($actions);

		return $condition;
	}

}
