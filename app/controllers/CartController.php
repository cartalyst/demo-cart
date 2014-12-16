<?php

use App\Models\Product;
use Cartalyst\Cart\Cart;
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
	 * @param  \Cartalyst\Cart\Cart  $cart
	 * @return void
	 */
	public function __construct(Cart $cart)
	{
		$this->cart = $cart;
	}

	/**
	 * Display a listing of products on the cart.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		$cart = $this->cart;

		$items = $cart->items();

		$total = $cart->total();

		$coupon = $cart->conditions('coupon');

		return View::make('cart.cart', compact('cart', 'items', 'total', 'coupon'));
	}

	/**
	 * Adds a new product to the shopping cart.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function add($id)
	{
		// Get the product from the database
		if ( ! $product = Product::find($id))
		{
			return Redirect::to('/');
		}

		$this->addToCart($product);

		return Redirect::to('cart')->withSuccess("{$product->name} was successfully added to the shopping cart.");
	}

	/**
	 * Move a product to the cart.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function move($id)
	{
		$itemId = $this->cart->item($id)->get('id');

		$this->delete($id);

		return Redirect::to("wishlist/$itemId/add");
	}

	/**
	 * Updates a product that is on the shopping cart.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update()
	{
		$this->cart->update(Input::get('update'));

		return Redirect::to('cart')->withSuccess('Cart was successfully updated.');
	}

	/**
	 * Deletes a product from the shopping cart.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$this->cart->remove($id);

		return Redirect::to('cart');
	}

	/**
	 * Destroys the shopping cart.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy()
	{
		$this->cart->clear();

		return Redirect::to('cart');
	}

	/**
	 * Adds a new product to the shopping cart.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function addAjax($id)
	{
		// Get the product from the database
		if ( ! $product = Product::find($id))
		{
			return json_encode([
				'error' => 'invalid product.',
			]);
		}

		// Add the item to the cart
		$item = $this->addToCart($product);

		return $item->toArray();
	}

	/**
	 * Deletes a product from the shopping cart.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function deleteAjax($id)
	{
		$item = $this->cart->item($id);

		$this->cart->remove($id);

		return ['message' => 'success', 'id' => $item->get('id')];
	}

	/**
	 * Deletes a product from the shopping cart.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function countAjax()
	{
		return $this->cart->items()->count();
	}

	/**
	 * Applies a coupon.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
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
			return Redirect::back()->withErrors("[{$code}] is not a valid coupon code.");
		}

		$couponCondition = new Condition($coupon['data']);

		$couponCondition->setRules($coupon['rules']);
		$couponCondition->setActions($coupon['actions']);

		$this->cart->condition($couponCondition);

		return Redirect::back()->withSuccess('Coupon was successfully applied.');
	}

	/**
	 * Removes the given coupon.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function removeCoupon($name)
	{
		$this->cart->removeConditionByName($name);

		return Redirect::back()->withSuccess('Coupon was successfully removed.');
	}

	/**
	 * Add product to cart.
	 *
	 * @param App\Models\Product  $product
	 * @return Cartalyst\Cart\Collections\ItemCollection
	 */
	protected function addToCart($product)
	{
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

		// Item conditions
		$condition1 = $this->createCondition('VAT (17.5%)', 'tax', 'subtotal', ['value' => '17.50%']);
		$condition2 = $this->createCondition('VAT (23%)', 'tax', 'subtotal', ['value' => '23%']);
		$condition3 = $this->createCondition('Discount (7.5%)', 'discount', 'subtotal', ['value' => '-7.5%']);
		$condition4 = $this->createCondition('Item Based Shipping', 'shipping', 'subtotal', ['value' => '20.00']);

		// Add the item to the cart
		$item = $this->cart->add([
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

		return $item;
	}

	/**
	 * Create a new condition.
	 *
	 * @param  string  $name
	 * @param  string  $type
	 * @param  string  $target
	 * @param  array  $actions
	 * @param  array  $rules
	 * @return \Cartalyst\Conditions\Condition
	 */
	protected function createCondition($name, $type, $target, $actions = [], $rules = [])
	{
		$condition = new Condition(compact('name', 'type', 'target'));

		$condition->setActions($actions);

		$condition->setRules($rules);

		return $condition;
	}

}
