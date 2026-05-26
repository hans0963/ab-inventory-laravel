<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_recipe_id',
        'raw_material_id',
        'quantity_per_unit',
    ];

    protected $casts = [
        'quantity_per_unit' => 'decimal:3',
    ];

    public function recipe()
    {
        return $this->belongsTo(ProductRecipe::class, 'product_recipe_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
