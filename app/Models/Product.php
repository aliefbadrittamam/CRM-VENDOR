<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name',
        'product_category',
        'product_price',
        'description'
    ];

    protected $casts = [
        'product_price' => 'decimal:2'
    ];

    // Relationship with SalesDetails
    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class, 'product_id', 'product_id');
    }
}