<?php

namespace App\Http\Controllers\Demo;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
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
        $this->middleware(function ($request, $next) {
            $this->wishlist = app('wishlist');

            return $next($request);
        });
    }

    /**
     * Display a listing of products on the wishlist.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('demo/wishlist', [
            'items' => $this->wishlist->items(),
            'total' => $this->wishlist->total(),
        ]);
    }

    /**
     * Adds a new product to the wishlist.
     *
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $id = $request->route('id');

        // Determine if this is an ajax request
        $isAjax = $request->ajax();

        // Get the product from the database
        $product = Product::find($id);

        // Check if the product exists on the database
        if (!$product) {
            if ($isAjax) {
                return response('Product was not found!', 404);
            }

            return redirect()->route('demo.home');
        }

        $item = $this->addToWishlist($product);

        if ($isAjax) {
            return response($item->toArray());
        }

        return redirect()->route('demo.wishlist')->withSuccess(
            "{$product->name} was successfully added to the wishlist."
        );
    }

    /**
     * Move a product to the cart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function move(Request $request)
    {
        $itemId = $this->wishlist->item($request->route('id'))->get('id');

        $this->delete($request);

        return redirect()->route('demo.cart.add', [$itemId]);
    }

    /**
     * Deletes a product from the wishlist.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $id = $request->route('id');

        $productId = $this->wishlist->item($id)->id;

        // Determine if this is an ajax request
        $isAjax = $request->ajax();

        // Get the product from the database
        $product = Product::find($productId);

        // Check if the product exists on the database
        if (!$product) {
            if ($isAjax) {
                return response('Product was not found!', 404);
            }

            return redirect()->route('demo.home');
        }

        $item = $this->wishlist->item($id);

        $this->wishlist->remove($id);

        if ($isAjax) {
            return response(['message' => 'success', 'id' => $item->get('id')]);
        }

        return redirect()->route('demo.wishlist')->withSuccess(
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

        return redirect()->route('demo.wishlist');
    }

    /**
     * Deletes a product from the shopping cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function count()
    {
        return $this->wishlist->items()->count();
    }

    /**
     * Add product to wishlist.
     *
     * @param \App\Product $product
     *
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
