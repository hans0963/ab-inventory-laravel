<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'raw_materials';
    protected $primaryKey = 'id';

    protected $fillable = [
        'material_name',
        'category_id',
        'type',
        'quantity',
        'unit',
        'expiration_date',
        'expiry_alert_days',
        'status',
        'stock_alert_threshold',
        'reorder_level',
        'reorder_quantity',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function movements()
    {
        return $this->hasMany(RawMaterialMovement::class);
    }

    public function recipeIngredients()
    {
        return $this->hasMany(ProductRecipeIngredient::class);
    }

    // Check if raw material is expired
    public function isExpired(): bool
    {
        return $this->expiration_date && now()->isAfter($this->expiration_date);
    }

    // Check if raw material is available
    public function isAvailable(): bool
    {
        return $this->status === 'Active' && !$this->isExpired();
    }
}
