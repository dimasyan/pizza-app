<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     *
     * @var array
     */
    protected $fillable = [

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['count']);
    }
}
