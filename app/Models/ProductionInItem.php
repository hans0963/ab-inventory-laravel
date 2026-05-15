<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionInItem extends Model
{
    use HasFactory;

    protected $table = 'production_in_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'production_in_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_value',
        'expiration_date'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function productionIn()
    {
        return $this->belongsTo(ProductionIn::class, 'production_in_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate total value
    public function calculateTotalValue()
    {
        $this->total_value = $this->quantity * $this->unit_price;
        $this->save();
        $this->productionIn->calculateTotalValue();
    }
}
