<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\StockMovementLogger;

class InventoryReceiving extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_receivings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'receiving_no',
        'date',
        'supplier_id',
        'purchase_id',
        'notes',
        'total_items',
        'total_cost',
        'status',
        'created_by',
        'created_date',
        'approved_by',
        'approved_date'
    ];

    protected $casts = [
        'date' => 'date',
        'created_date' => 'date',
        'approved_date' => 'datetime',
        'total_cost' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(InventoryReceivingItem::class, 'inventory_receiving_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function purchaseReceivingLinks()
    {
        return $this->hasMany(PurchaseReceivingLink::class, 'inventory_receiving_id');
    }

    public function purchases()
    {
        return $this->belongsToMany(
            Purchase::class,
            'purchase_receiving_links',
            'inventory_receiving_id',
            'purchase_id'
        )->withPivot('items_received', 'amount_received')->withTimestamps();
    }

    // Generate unique receiving number
    public static function generateReceivingNo(): string
    {
        $prefix = 'PRC-' . now()->format('Ymd');
        $lastRecord = self::where('created_at', '>=', now()->startOfDay())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastRecord ? ((int)substr($lastRecord->receiving_no, -3)) + 1 : 1;
        return $prefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    // Calculate total cost from items
    public function calculateTotalCost()
    {
        $total = $this->items()->sum('total_cost');
        $this->total_cost = $total;
        $this->save();
        return $total;
    }

    // Calculate total items count
    public function calculateTotalItems()
    {
        $count = $this->items()->sum('quantity_received');
        $this->total_items = $count;
        $this->save();
        return $count;
    }

    // Check if can be approved
    public function canBeApproved(): bool
    {
        return $this->status === 'Pending' && $this->items()->count() > 0;
    }

    // Approve receiving and update product quantities
    public function approve(User $user)
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->status = 'Approved';
        $this->approved_by = $user->id;
        $this->approved_date = now();
        $this->save();

        // Update product quantities for items with "Good" condition
        foreach ($this->items()->where('condition', 'Good')->get() as $item) {
            $product = $item->product;
            $quantityBefore = $product->quantity;
            $product->quantity += $item->quantity_received;
            $product->save();
            StockMovementLogger::record(
                $product,
                $quantityBefore,
                $item->quantity_received,
                'IN',
                'INVENTORY RECEIVING',
                $this,
                null,
                $user->id
            );
        }

        return true;
    }

    // Reject receiving
    public function reject(User $user)
    {
        $this->status = 'Rejected';
        $this->approved_by = $user->id;
        $this->approved_date = now();
        $this->save();

        return true;
    }
}
