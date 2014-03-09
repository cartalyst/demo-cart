<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

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
