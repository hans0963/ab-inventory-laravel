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
        'discount_type',
        'discount_percentage',
        'discount_value',
        'minimum_purchase_amount',
        'applicable_to',
        'applicable_ids',
        'start_date',
        'end_date',
        'description',
        'status'
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'minimum_purchase_amount' => 'decimal:2',
        'applicable_ids' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with Sales
    public function sales()
    {
        return $this->hasMany(Sale::class, 'discount_type_id');
    }

    public function getEffectiveStatusAttribute(): string
    {
        if ($this->end_date && $this->end_date->isPast() && $this->status === 'Active') {
            return 'Expired';
        }

        return $this->status;
    }

    public function getDisplayValueAttribute(): string
    {
        if ($this->discount_type === 'Fixed Amount') {
            return 'PHP ' . number_format((float) $this->discount_value, 2);
        }

        return rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.') . '%';
    }
}
