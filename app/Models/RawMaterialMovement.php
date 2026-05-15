<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'user_id',
        'quantity',
        'type',
        'reason',
        'date'
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
