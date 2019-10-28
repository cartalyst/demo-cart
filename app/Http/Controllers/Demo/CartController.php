<?php

namespace App\Http\Controllers\Demo;

use App\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Cartalyst\Conditions\Condition;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * The Cart instance.
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
        $this->middleware(function ($request, $next) {
            $this->cart = app('cart');

            return $next($request);
        });
    }

    /**
     * Display a listing of products on the cart.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $items = $this->cart->items();

        $total = $this->cart->total();

        $coupon = $this->cart->conditions('coupon');

        return view('demo/cart', compact('items', 'total', 'coupon'));
    }

    /**
     * Adds a new product to the shopping cart.
     *
     * @param \Illuminate\Http\Request $request
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
        if (! $product) {
            if ($isAjax) {
                return response('Product was not found!', 404);
            }

            return redirect()->route('demo.home');
        }

        $item = $this->addToCart($product);

        if ($isAjax) {
            return response($item->toArray());
        }

        return redirect()->route('demo.cart')->withSuccess(
            "{$product->name} was successfully added to the shopping cart."
        );
    }

    /**
     * Move a product to the wishlist.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function move(string $id)
    {
        $itemId = $this->cart->item($id)->get('id');

        $this->cart->remove($id);

        return redirect()->route('demo.wishlist.add', [ $itemId ]);
    }

    /**
     * Updates a product that is on the shopping cart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->cart->update($request->input('update'));

        return redirect()->route('demo.cart')->withSuccess('Shopping cart was successfully updated.');
    }

    /**
     * Deletes a product from the shopping cart.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $id = $request->route('id');

        $productId = $this->cart->item($id)->id;

        // Determine if this is an ajax request
        $isAjax = $request->ajax();

        // Get the product from the database
        $product = Product::find($productId);

        // Check if the product exists on the database
        if (! $product) {
            if ($isAjax) {
                return response('Product was not found!', 404);
            }

            return redirect()->route('demo.home');
        }

        $item = $this->cart->item($id);

        $this->cart->remove($id);

        if ($isAjax) {
            return response(['message' => 'success', 'id' => $item->get('id')]);
        }

        return redirect()->route('demo.cart')->withSuccess(
            "{$product->name} was successfully removed from the shopping cart."
        );
    }

    /**
     * Destroys the shopping cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $this->cart->clear();

        return redirect()->route('demo.cart')->withSuccess('Shopping cart was successfully cleared.');
    }

    /**
     * Deletes a product from the shopping cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function count()
    {
        return $this->cart->items()->count();
    }

    /**
     * Applies a coupon.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyCoupon(Request $request)
    {
        $code = $request->input('coupon');

        $coupons = getCouponsList();

        if (! $coupon = Arr::get($coupons, $code)) {
            return redirect()->back()->withErrors("[{$code}] is not a valid coupon code.");
        }

        $couponCondition = new Condition($coupon['data']);
        $couponCondition->setRules($coupon['rules']);
        $couponCondition->setActions($coupon['actions']);

        $this->cart->condition($couponCondition);

        return redirect()->back()->withSuccess('Coupon was successfully applied.');
    }

    /**
     * Removes the given coupon.
     *
     * @param string $name
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeCoupon(string $name)
    {
        $this->cart->removeConditionByName($name);

        return redirect()->back()->withSuccess('Coupon was successfully removed.');
    }

    /**
     * Add product to cart.
     *
     * @param \App\Product $product
     *
     * @return \Cartalyst\Cart\Collections\ItemCollection
     */
    protected function addToCart(Product $product)
    {
        $condition1 = createCondition('Item Based Shipping', 'shipping', 'subtotal', [ 'value' => '20.00'  ]);

        // Add the item to the cart
        return $this->cart->add([
            'quantity'   => 1,
            'id'         => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'conditions' => [$condition1],
        ]);
    }
}
