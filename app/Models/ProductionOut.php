<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\StockMovementLogger;

class ProductionOut extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'production_outs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'production_out_no',
        'date',
        'reason',
        'notes',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(ProductionOutItem::class, 'production_out_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Generate unique production out number
    public static function generateProductionOutNo(): string
    {
        $prefix = 'POUT-' . now()->format('Ymd');
        $lastRecord = self::where('created_at', '>=', now()->startOfDay())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastRecord ? ((int)substr($lastRecord->production_out_no, -3)) + 1 : 1;
        return $prefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    // Check if can be approved
    public function canBeApproved(): bool
    {
        return $this->status === 'Pending' && $this->items()->count() > 0;
    }

    // Approve production out
    public function approve(User $user): void
    {
        if ($this->canBeApproved()) {
            $this->status = 'Approved';
            $this->approved_by = $user->id;
            $this->approved_date = now();
            $this->save();

            foreach ($this->items()->with('product')->get() as $item) {
                if ($item->product->quantity < $item->quantity) {
                    throw new \RuntimeException("Insufficient stock. Current stock: {$item->product->quantity}, Requested: {$item->quantity}. Transaction cannot be completed.");
                }
            }

            foreach ($this->items()->with('product')->get() as $item) {
                $quantityBefore = $item->product->quantity;
                $item->product->decrement('quantity', $item->quantity);
                StockMovementLogger::record(
                    $item->product,
                    $quantityBefore,
                    -$item->quantity,
                    'OUT',
                    $this->reason ?? 'PRODUCTION OUT',
                    $this,
                    null,
                    $user->id
                );
            }
        }
    }

    // Reject production out
    public function reject(User $user): void
    {
        if ($this->status === 'Pending') {
            $this->status = 'Rejected';
            $this->save();
        }
    }
}
