<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockWithdrawalItem extends Model
{
    use HasFactory;

    protected $table = 'stock_withdrawal_items';
    protected $primaryKey = 'id';

    protected $fillable = [
        'stock_withdrawal_id',
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
    public function stockWithdrawal()
    {
        return $this->belongsTo(StockWithdrawal::class, 'stock_withdrawal_id');
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
        $this->stockWithdrawal->calculateTotals();
    }
}
