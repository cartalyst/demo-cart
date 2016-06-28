<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
	/**
	 * {@inheritdoc}
	 */
	public $table = 'carts';

	/**
	 * {@inheritdoc}
	 */
	protected $fillable = [ 'instance' ];

	/**
	 * {@inheritdoc}
	 */
	public function delete()
	{
		$this->items()->delete();

		return parent::delete();
	}

    /**
     * Returns the items that belongs to this cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function items()
	{
		return $this->hasMany(CartItem::class, 'cart_id');
	}
}
