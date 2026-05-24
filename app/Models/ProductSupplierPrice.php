<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSupplierPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'unit_price',
        'unit',
        'is_default',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_default' => 'boolean',
    ];
}
