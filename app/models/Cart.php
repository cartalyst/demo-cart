<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

	/**
	 * {@inheritDoc}
	 */
	public $fillable = [
		'instance',
	];

	public function delete()
	{
		$this->items()->delete();

		return parent::delete();
	}

	public function items()
	{
		return $this->hasMany('App\Models\CartItem', 'cart_id');
	}

}
