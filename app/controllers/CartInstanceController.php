<?php

class CartInstanceController extends BaseController {

	protected $instance;

	public function __construct()
	{
		$this->instance = Request::segment(1);
	}

	public function index()
	{
		$cart = Cart::instance($this->instance);

		$items = $cart->items();

		$total = $cart->total();

		return View::make('cart.wishlist', compact('cart', 'items', 'total'));
	}

	public function add($id)
	{
		$product = Product::where('slug', $id)->first();

		$data = array(
			'id'       => $product->slug,
			'name'     => $product->name,
			'price'    => $product->price,
			'quantity' => 1,
		);

		Cart::instance($this->instance)->add($data);

		return Redirect::to($this->instance);
	}

	public function delete($id)
	{
		Cart::instance($this->instance)->remove($id);

		return Redirect::to($this->instance);
	}

	public function destroy()
	{
		Cart::instance($this->instance)->destroy();

		return Redirect::to($this->instance);
	}

}
