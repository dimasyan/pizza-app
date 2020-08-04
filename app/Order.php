<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const DELIVERY_PRICE = 5;

    /**
     *
     * @var array
     */
    protected $fillable = [
        'status', 'delivery_price', 'products_cost', 'total_cost',
        'address', 'city', 'country', 'postcode', 'email', 'phone', 'name'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['price', 'count', 'total']);
    }
}
