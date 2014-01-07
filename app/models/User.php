<?php

class User extends Cartalyst\Sentry\Users\EloquentUser {

	public function cart()
	{
		return $this->hasMany('App\Models\Cart');
	}

}
