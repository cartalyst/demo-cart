<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {

	public $table = 'carts_items';

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

}
