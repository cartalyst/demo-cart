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
		$wishlist = $this->wishlist;

		$items = $this->wishlist->items();

		$total = $this->wishlist->total();

		return View::make('cart.wishlist', compact('wishlist', 'items', 'total'));
	}

	/**
	 * Adds a new product to the wishlist.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function add($id)
	{
		$product = Product::find($id);

		$this->addToWishlist($product);

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
		$this->wishlist->remove($id);

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
		$item = $this->addToWishlist($product);

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
		$item = $this->wishlist->item($id);

		$this->wishlist->remove($id);

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
		return $this->wishlist->items()->count();
	}

	/**
	 * Add product to wishlist.
	 *
	 * @param App\Models\Product  $product
	 * @return Cartalyst\Cart\Collections\ItemCollection
	 */
	protected function addToWishlist($product)
	{
		return $this->wishlist->add([
			'id'         => $product->id,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
		]);
	}

}
