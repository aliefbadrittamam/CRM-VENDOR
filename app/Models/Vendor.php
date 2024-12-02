<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    // Tentukan kolom primary key yang benar
    protected $primaryKey = 'vendor_id';  // Sesuaikan dengan nama kolom primary key

    // Jika kolom primary key bukan integer, Anda bisa mengubah tipe datanya
    protected $keyType = 'int';
}
