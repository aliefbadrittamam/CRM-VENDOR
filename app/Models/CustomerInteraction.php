<?php

// app/Models/CustomerInteraction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInteraction extends Model
{
    protected $primaryKey = 'interaction_id';
    public $timestamps = false;
    
    protected $fillable = [
        'customer_id',
        'user_id',
        'vendor_id',
        'interaction_type',
        'interaction_date',
        'notes'
    ];

    // Tambahkan ini untuk mengubah interaction_date menjadi instance Carbon
    protected $casts = [
        'interaction_date' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}