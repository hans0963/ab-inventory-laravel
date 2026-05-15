<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryReceivingItem extends Model
{
    use HasFactory;

    protected $table = 'inventory_receiving_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'inventory_receiving_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'expiration_date',
        'condition'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'unit_cost' => 'float',
        'total_cost' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function inventoryReceiving()
    {
        return $this->belongsTo(InventoryReceiving::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Calculate and update total cost
    public function calculateTotalCost()
    {
        $this->total_cost = $this->quantity_received * $this->unit_cost;
        $this->save();
        $this->inventoryReceiving->calculateTotalCost();
    }
}
