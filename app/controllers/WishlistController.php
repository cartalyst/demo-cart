<?php

use App\Models\Product;

class WishlistController extends BaseController {

	public function index()
	{
		$cart = app('wishlist');

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

		app('wishlist')->add($data);

		return Redirect::back();
	}

	public function delete($id)
	{
		$product = Product::where('slug', $id)->first();

		$data = array(
			'id'       => $product->slug,
			'name'     => $product->name,
			'quantity' => 1,
		);

		$rowId = head(app('wishlist')->find($data))->get('rowId');

		app('wishlist')->remove($rowId);

		return Redirect::back();
	}

	public function destroy()
	{
		app('wishlist')->clear();

		return Redirect::to('wishlist');
	}

}
