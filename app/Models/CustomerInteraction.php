<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInteraction extends Model
{
    protected $primaryKey = 'interaction_id';

    protected $fillable = [
        'customer_id',
        'user_id',
        'interaction_type',
        'interaction_date',
        'notes'
    ];

    protected $casts = [
        'interaction_date' => 'datetime'
    ];

    // Relasi yang diperlukan
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}