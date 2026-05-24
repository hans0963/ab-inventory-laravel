<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_date',
        'expected_delivery_date',
        'supplier_id',
        'employee_id',
        'reference',
        'po_number',
        'status',
        'total_amount',
        'notes',
        'created_by',
        'created_date',
        'approved_by',
        'approved_date',
        'rejected_by',
        'rejected_date',
        'rejection_reason',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
        'created_date' => 'datetime',
        'approved_date' => 'datetime',
        'rejected_date' => 'datetime',
        'total_amount' => 'float',
    ];

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivingLinks()
    {
        return $this->hasMany(PurchaseReceivingLink::class);
    }

    public function inventoryReceivings()
    {
        return $this->belongsToMany(
            InventoryReceiving::class,
            'purchase_receiving_links',
            'purchase_id',
            'inventory_receiving_id'
        )->withPivot('items_received', 'amount_received')->withTimestamps();
    }

    /**
     * Generate unique PO number with format PO-YYYYMMDD-XXX
     */
    public static function generatePONumber(?string $date = null): string
    {
        $dateObj = $date ? \Carbon\Carbon::parse($date) : now();
        $dateStr = $dateObj->format('Ymd');
        
        // Count POs created on that date to generate sequence
        $count = self::whereDate('created_date', $dateObj->toDateString())->count() + 1;
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        return "PO-{$dateStr}-{$sequence}";
    }

    /**
     * Calculate total amount from purchase details
     */
    public function calculateTotal(): float
    {
        return (float) $this->details()->sum('total');
    }

    /**
     * Update total amount and save
     */
    public function updateTotal(): void
    {
        $this->update(['total_amount' => $this->calculateTotal()]);
    }

    /**
     * Get total quantity ordered across all items
     */
    public function getTotalQuantityOrdered(): int
    {
        return (int) $this->details()->sum('quantity');
    }

    /**
     * Get total quantity received via linked receivings (Good condition only)
     */
    public function getTotalQuantityReceived(): int
    {
        $total = 0;
        foreach ($this->inventoryReceivings as $receiving) {
            $total += $receiving->items()
                ->where('condition', 'Good')
                ->sum('quantity_received');
        }
        return $total;
    }

    /**
     * Get reception progress percentage
     */
    public function getReceptionProgress(): int
    {
        $ordered = $this->getTotalQuantityOrdered();
        if ($ordered === 0) {
            return 0;
        }
        
        $received = $this->getTotalQuantityReceived();
        return (int) round(($received / $ordered) * 100);
    }

    /**
     * Check if purchase can accept more receivings
     */
    public function canReceive(): bool
    {
        return $this->status !== 'Complete' && $this->getTotalQuantityReceived() < $this->getTotalQuantityOrdered();
    }

    /**
     * Auto-update status based on reception progress
     */
    public function updateStatus(): void
    {
        $ordered = $this->getTotalQuantityOrdered();
        $received = $this->getTotalQuantityReceived();

        if ($received === 0) {
            $this->status = in_array($this->status, ['Approved', 'Ordered'], true) ? $this->status : 'Pending Approval';
        } elseif ($received >= $ordered) {
            $this->status = 'Complete';
        } else {
            $this->status = 'Partial';
        }

        $this->save();
    }

    /**
     * Link a receiving to this purchase
     */
    public function linkReceiving(InventoryReceiving $receiving, int $itemsReceived, float $amountReceived): void
    {
        PurchaseReceivingLink::updateOrCreate(
            [
                'purchase_id' => $this->id,
                'inventory_receiving_id' => $receiving->id,
            ],
            [
                'items_received' => $itemsReceived,
                'amount_received' => $amountReceived,
            ]
        );

        $this->updateStatus();
    }

    /**
     * Unlink a receiving from this purchase
     */
    public function unlinkReceiving(InventoryReceiving $receiving): void
    {
        PurchaseReceivingLink::where('purchase_id', $this->id)
            ->where('inventory_receiving_id', $receiving->id)
            ->delete();

        $this->updateStatus();
    }
}
