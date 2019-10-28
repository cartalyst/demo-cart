<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'instance',
    ];

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
