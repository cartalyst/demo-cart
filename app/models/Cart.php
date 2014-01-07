<?php namespace App\Models;

use Eloquent;

class Cart extends Eloquent {

	public function delete()
	{
		foreach($this->items as $item)
		{
			$item->delete();
		}

		return parent::delete();
	}

	public function items()
	{
		return $this->hasMany('App\Models\CartItem', 'cart_id');
	}

}
