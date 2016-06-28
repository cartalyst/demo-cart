<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CreateDefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('carts')->truncate();
		DB::table('carts_items')->truncate();
		DB::table('users')->truncate();

		$users = [
			[ 'email'    => 'admin@admin.com'  , 'password' => 'password' ],
			[ 'email'    => 'demo1@example.com', 'password' => 'password' ],
			[ 'email'    => 'demo2@example.com', 'password' => 'password' ],
		];

		foreach ($users as $user) {
			Sentinel::registerAndActivate($user);
		}
    }
}
