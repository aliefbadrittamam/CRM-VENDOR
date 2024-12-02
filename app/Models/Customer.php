<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Relasi dengan Projects
    public function projects()
    {
        return $this->hasMany(Project::class, 'customer_id', 'id');
    }
}
