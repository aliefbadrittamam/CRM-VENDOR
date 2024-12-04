<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInteraction extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'vendor_id',
        'interaction_type',
        'interaction_date',
        'notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}