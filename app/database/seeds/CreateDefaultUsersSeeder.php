<?php

class CreateDefaultUsersSeeder extends Seeder {

	public function run()
	{
		DB::table('carts')->truncate();
		DB::table('carts_items')->truncate();
		DB::table('users')->truncate();

		$users = [

			[
				'email'    => 'admin@admin.com',
				'password' => 'password',
			],

			[
				'email'    => 'demo1@example.com',
				'password' => 'demo123',
			],

			[
				'email'    => 'demo2@example.com',
				'password' => 'demo123',
			],

		];

		foreach ($users as $user)
		{
			Sentinel::registerAndActivate($user);
		}
	}

}
