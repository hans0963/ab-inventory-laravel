<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOutItem extends Model
{
    use HasFactory;

    protected $table = 'production_out_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'production_out_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_value'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function productionOut()
    {
        return $this->belongsTo(ProductionOut::class, 'production_out_id');
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
    }
}
