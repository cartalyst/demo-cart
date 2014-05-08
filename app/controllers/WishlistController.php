<?php

use App\Models\Product;

class WishlistController extends BaseController {

	/**
	 * The wishlist instance.
	 *
	 * @var \Cartalyst\Cart\Cart
	 */
	protected $wishlist;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->wishlist = app('wishlist');
	}

	/**
	 * Display a listing of products on the wishlist.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		$items = $this->wishlist->items();

		$total = $this->wishlist->total();

		return View::make('cart.wishlist', compact('items', 'total'));
	}

	/**
	 * Adds a new product to the wishlist.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function add($id)
	{
		$product = Product::find($id);

		$data = [
			'id'       => $product->id,
			'name'     => $product->name,
			'price'    => $product->price,
			'quantity' => 1,
		];

		$this->wishlist->add($data);

		return Redirect::back();
	}

	/**
	 * Deletes a product from the wishlist.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$product = Product::find($id);

		$data = [
			'id'       => $product->id,
			'name'     => $product->name,
			'quantity' => 1,
		];

		$rowId = head($this->wishlist->find($data))->get('rowId');

		$this->wishlist->remove($rowId);

		return Redirect::back();
	}

	/**
	 * Destroys the wishlist.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy()
	{
		$this->wishlist->clear();

		return Redirect::to('wishlist');
	}

}
