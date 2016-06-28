<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
	/**
	 * {@inheritdoc}
	 */
	public $table = 'carts_items';

	/**
	 * {@inheritdoc}
	 */
	protected $fillable = [ 'product_id', 'quantity' ];

	public function product()
	{
		return $this->belongsTo(Product::class, 'product_id');
	}
}
