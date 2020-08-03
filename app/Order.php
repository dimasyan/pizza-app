<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     *
     * @var array
     */
    protected $fillable = [
        'status', 'delivery_price', 'products_cost', 'total_cost',
        'address', 'city', 'country', 'postcode'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['price', 'count', 'total']);
    }
}
