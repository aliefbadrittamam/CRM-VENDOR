<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInteraction extends Model
{
    protected $guarded = [];

    // Relasi ke Customer (many-to-one)
    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke User (many-to-one)
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Vendor (many-to-one)
    public function vendors()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
