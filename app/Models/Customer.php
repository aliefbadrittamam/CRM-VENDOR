<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function interactions()
    {
        return $this->hasMany(CustomerInteraction::class, 'customer_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}