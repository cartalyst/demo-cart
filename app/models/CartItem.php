<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {

	/**
	 * {@inheritDoc}
	 */
	public $table = 'carts_items';

	/**
	 * {@inheritDoc}
	 */
	public $fillable = [
		'product_id',
		'quantity',
	];

	public function product()
	{
		return $this->belongsTo('App\Models\Product');
	}

}
