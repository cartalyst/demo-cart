<?php

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('products')->truncate();

		$faker = Faker\Factory::create();
		$lorem = new Faker\Provider\Lorem($faker);
		$random = new Faker\Provider\Base($faker);

		for ($i=0; $i < 500; $i++){
			$name = $lorem->word();

			$product = new Product;
			$product->slug  = str_slug($name);
			$product->name  = ucfirst($name);
			$product->price = $random->randomNumber(2);
			$product->save();
		}
    }
}
