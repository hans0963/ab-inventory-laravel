<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReceivingLink extends Model
{
    use HasFactory;

    protected $table = 'purchase_receiving_links';

    protected $fillable = [
        'purchase_id',
        'inventory_receiving_id',
        'items_received',
        'amount_received',
    ];

    protected $casts = [
        'items_received' => 'integer',
        'amount_received' => 'float',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function inventoryReceiving()
    {
        return $this->belongsTo(InventoryReceiving::class);
    }
}
