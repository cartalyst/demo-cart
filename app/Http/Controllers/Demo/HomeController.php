<?php

namespace App\Http\Controllers\Demo;

use App\Product;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::paginate(20);

        return view('demo/products', ['products' => $products]);
    }
}
