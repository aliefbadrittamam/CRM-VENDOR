<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'project_id';

    protected $fillable = [
        'vendor_id',
        'customer_id',
        'product_id',
        'project_header',
        'project_value',
        'project_duration_start',
        'project_duration_end',
        'project_detail'
    ];

    protected $casts = [
        'project_value' => 'decimal:2',
        'project_duration_start' => 'datetime',
        'project_duration_end' => 'datetime'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'project_id', 'project_id');
    }

    public function priceQuotations()
    {
        return $this->hasMany(PriceQuotation::class, 'project_id', 'project_id');
    }
}