<?php

class HomeController extends BaseController {

	public function index()
	{
		$cart = app('cart');

		$products = Product::paginate(20);

		return View::make('cart.products', compact('cart', 'products'));
	}

}
