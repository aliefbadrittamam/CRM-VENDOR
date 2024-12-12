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

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_product', 'product_id', 'project_id')
                    ->withPivot(['quantity', 'price_at_time', 'subtotal'])
                    ->withTimestamps();
    }

    public function salesDetails() 
    {
        return $this->hasMany(SalesDetail::class, 'product_id', 'product_id');
    }
}