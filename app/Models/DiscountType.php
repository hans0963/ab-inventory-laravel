<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountType extends Model
{
    use HasFactory;

    protected $table = 'discount_types';
    protected $primaryKey = 'id';

    protected $fillable = [
        'discount_name',
        'discount_percentage',
        'description',
        'status'
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with Sales
    public function sales()
    {
        return $this->hasMany(Sale::class, 'discount_type_id');
    }
}
