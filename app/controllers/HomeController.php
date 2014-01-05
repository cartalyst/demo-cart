<?php

class HomeController extends BaseController {

	public function index()
	{
		$cart = Cart::instance('main');

		$products = Product::paginate(20);

		return View::make('cart.products', compact('cart', 'products'));
	}

	public function product($id)
	{

	}

}
