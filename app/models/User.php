<?php

class User extends Cartalyst\Sentinel\Users\EloquentUser {

	public function cart()
	{
		return $this->hasMany('App\Models\Cart');
	}

}
