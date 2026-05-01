<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = ['material_name', 'quantity', 'unit', 'expiration_date'];
}
