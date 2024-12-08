<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerInteraction extends Model
{
    use HasFactory;

    protected $primaryKey = 'interaction_id';
    
    // Pastikan semua field yang diperlukan ada di fillable
    protected $fillable = [
        'customer_id',
        'user_id',
        'vendor_id',
        'interaction_type',
        'interaction_date',
        'notes'
    ];

    protected $casts = [
        'interaction_date' => 'datetime'
    ];

    // Tambahkan properti timestamps jika menggunakan created_at dan updated_at
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}