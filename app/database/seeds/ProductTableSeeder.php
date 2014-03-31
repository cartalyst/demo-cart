<?php

use App\Models\Product;

class ProductTableSeeder extends Seeder {

	public function run()
	{
		DB::table('products')->truncate();

		$faker = Faker\Factory::create();
		$lorem = new Faker\Provider\Lorem($faker);
		$random = new Faker\Provider\Base($faker);

		for ($i=0; $i < 500; $i++)
		{
			$name = $lorem->word();

			$product = new Product;
			$product->slug = Str::slug($name);
			$product->name  = ucfirst($name);
			$product->price = $random->randomNumber(2);
			$product->save();
		}
	}

}
