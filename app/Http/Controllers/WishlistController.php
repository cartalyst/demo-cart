<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller {

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

		return view('cart.wishlist', compact('items', 'total'));
	}

	/**
	 * Adds a new product to the wishlist.
	 *
     * @param  string  $id
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function add($id, Request $request)
	{
        // Determine if this is an ajax request
        $isAjax = $request->ajax();

        // Get the product from the database
        $product = Product::find($id);

        // Check if the product exists on the database
        if (! $product) {
            if ($isAjax) {
                return response('Product was not found!', 404);
            }

            return redirect()->to('/');
        }

		$item = $this->addToWishlist($product);

        if ($isAjax) {
            return response($item->toArray());
        }

        return redirect()->route('wishlist')->withSuccess(
            "{$product->name} was successfully added to the wishlist."
        );
	}

	/**
	 * Move a product to the cart.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function move($id)
	{
		$itemId = $this->wishlist->item($id)->get('id');

		$this->delete($id);

		return redirect()->to("cart/$itemId/add");
	}

	/**
	 * Deletes a product from the wishlist.
	 *
	 * @param  string  $id
     * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id, Request $request)
	{
        $productId = $this->wishlist->item($id)->id;

        // Determine if this is an ajax request
        $isAjax = $request->ajax();

        // Get the product from the database
        $product = Product::find($productId);

        // Check if the product exists on the database
        if (! $product) {
            if ($isAjax) {
                return response('Product was not found!', 404);
            }

            return redirect()->to('/');
        }

        $item = $this->wishlist->item($id);

        $this->wishlist->remove($id);

        if ($isAjax) {
            return response([ 'message' => 'success', 'id' => $item->get('id') ]);
        }

        return redirect()->route('wishlist')->withSuccess(
            "{$product->name} was successfully removed from the wishlist."
        );
	}

	/**
	 * Destroys the wishlist.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function destroy()
	{
		$this->wishlist->clear();

		return redirect()->route('wishlist');
	}

	/**
	 * Deletes a product from the shopping cart.
	 *
	 * @param  string  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function count()
	{
		return $this->wishlist->items()->count();
	}

	/**
	 * Add product to wishlist.
	 *
	 * @param  \App\Models\Product  $product
	 * @return \Cartalyst\Cart\Collections\ItemCollection
	 */
	protected function addToWishlist(Product $product)
	{
		return $this->wishlist->add([
			'id'         => $product->id,
			'name'       => $product->name,
			'price'      => $product->price,
			'quantity'   => 1,
		]);
	}
}
