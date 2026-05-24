<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovementLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'employee_id',
        'user_id',
        'source_type',
        'source_id',
        'movement_date',
        'quantity_before',
        'quantity_changed',
        'quantity_after',
        'movement_type',
        'reason',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];
}
