<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipping extends Model
{
    protected $table = 'shipping';
    protected $primaryKey = 'shipping_id';
    public $timestamps = false;

    protected $fillable = [
        'purchase_detail_id',
        'project_id',
        'vendor_id', 
        'customer_id',
        'shipping_status',
        'Number_receipt'
    ];

    protected $casts = [
        'shipping_status' => 'string',
        'Number_receipt' => 'integer'
    ];

    public function purchaseDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseDetail::class, 'purchase_detail_id', 'purchase_detail_id');
    }

    public function project(): BelongsTo 
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}