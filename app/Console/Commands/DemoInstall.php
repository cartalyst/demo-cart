<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DemoInstall extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'demo:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install demo.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->call('migrate', [ '--seed' => true ]);
	}
}
