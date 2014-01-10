<?php

class WishlistController extends BaseController {

	public function index()
	{
		$cart = Cart::instance('wishlist');

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

		Cart::instance('wishlist')->add($data);

		return Redirect::to('wishlist');
	}

	public function delete($id)
	{
		Cart::instance('wishlist')->remove($id);

		return Redirect::to('wishlist');
	}

	public function destroy()
	{
		Cart::instance('wishlist')->clear();

		return Redirect::to('wishlist');
	}

}
