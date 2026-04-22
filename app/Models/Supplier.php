<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'suppliers_name', 
        'suppliers_company', 
        'suppliers_email', 
        'suppliers_phone', 
        'suppliers_address'
    ];
}

