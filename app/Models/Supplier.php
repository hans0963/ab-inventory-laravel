<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'suppliers_name',
        'suppliers_company',
        'suppliers_email',
        'suppliers_phone',
        'suppliers_address',
        'items_supplied',
        'payment_terms',
        'status',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
