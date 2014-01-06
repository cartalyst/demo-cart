<?php

class CreateDefaultUsersSeeder extends Seeder {

	public function run()
	{
		$users = array(
			array(
				'email'    => 'admin@admin.com',
				'password' => 'password',
			),
			array(
				'email'    => 'demo1@example.com',
				'password' => 'demo123',
			),
			array(
				'email'    => 'demo2@example.com',
				'password' => 'demo123',
			),
		);

		foreach ($users as $user)
		{
			Sentry::registerAndActivate($user);
		}
	}

}
