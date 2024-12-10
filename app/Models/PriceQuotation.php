<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class PriceQuotation extends Model
{
    protected $table = 'price_quotations'; // Sesuaikan nama tabel yang benar
    protected $primaryKey = 'price_quotation_id';
    
    protected $fillable = [
        'project_id',
        'vendor_id',
        'amount'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }
}