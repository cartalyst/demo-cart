<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Cartalyst\Cart\Cart;
use Illuminate\Http\Request;
use Cartalyst\Conditions\Condition;

class CartController extends Controller
{
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
        $items = $this->cart->items();

        $total = $this->cart->total();

        $coupon = $this->cart->conditions('coupon');

        return view('cart/cart', compact('items', 'total', 'coupon'));
    }

    /**
     * Adds a new product to the shopping cart.
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

        $item = $this->addToCart($product);

        if ($isAjax) {
            return response($item->toArray());
        }

        return redirect()->route('cart')->withSuccess(
            "{$product->name} was successfully added to the shopping cart."
        );
    }

    /**
     * Move a product to the wishlist.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function move($id)
    {
        $itemId = $this->cart->item($id)->get('id');

        $this->cart->remove($id);

        return redirect()->route('wishlist.add-item', [ $itemId ]);
    }

    /**
     * Updates a product that is on the shopping cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->cart->update($request->get('update'));

        return redirect()->route('cart')->withSuccess('Shopping cart was successfully updated.');
    }

    /**
     * Deletes a product from the shopping cart.
     *
     * @param  string  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, Request $request)
    {
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

            return redirect()->to('/');
        }

        $item = $this->cart->item($id);

        $this->cart->remove($id);

        if ($isAjax) {
            return response([ 'message' => 'success', 'id' => $item->get('id') ]);
        }

        return redirect()->route('cart')->withSuccess(
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

        return redirect()->route('cart')->withSuccess('Shopping cart was successfully cleared.');
    }

    /**
     * Deletes a product from the shopping cart.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function count()
    {
        return $this->cart->items()->count();
    }

    /**
     * Applies a coupon.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyCoupon(Request $request)
    {
        $code = $request->get('coupon');

        $coupons = getCouponsList();

        if (! $coupon = array_get($coupons, $code)) {
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeCoupon($name)
    {
        $this->cart->removeConditionByName($name);

        return redirect()->back()->withSuccess('Coupon was successfully removed.');
    }

    /**
     * Add product to cart.
     *
     * @param  \App\Models\Product  $product
     * @return \Cartalyst\Cart\Collections\ItemCollection
     */
    protected function addToCart(Product $product)
    {
        // Set the global conditions order
        $this->cart->setConditionsOrder([
            'discount', 'other', 'tax', 'shipping', 'coupon',
        ]);

        // Set the items conditions order
        $this->cart->setItemsConditionsOrder([
            'discount', 'other', 'tax', 'shipping',
        ]);

        // Item conditions
        $condition1 = createCondition('VAT (17.5%)'        , 'tax'     , 'subtotal', [ 'value' => '17.50%' ]);
        $condition2 = createCondition('VAT (23%)'          , 'tax'     , 'subtotal', [ 'value' => '23%'    ]);
        $condition3 = createCondition('Discount (7.5%)'    , 'discount', 'subtotal', [ 'value' => '-7.5%'  ]);
        $condition4 = createCondition('Item Based Shipping', 'shipping', 'subtotal', [ 'value' => '20.00'  ]);

        // Add the item to the cart
        return $this->cart->add([
            'id'         => $product->id,
            'name'       => $product->name,
            'price'      => $product->price,
            'quantity'   => 1,
            'conditions' => [ $condition1, $condition2, $condition3, $condition4 ],
        ]);
    }
}
