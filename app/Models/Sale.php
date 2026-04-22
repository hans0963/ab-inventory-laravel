<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'employee_id',
        'date',
        'sold',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

