<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'employee_id',
        'supplier_id',
        'balance_forwarded',
        'pull_out',
        'new_balance',
        'new_luto',
        'total_inventory',
        'transaction_type',
        'date'
    ];

    // Define relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
