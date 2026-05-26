<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\StockMovementLogger;
use App\Models\RawMaterial;
use App\Models\RawMaterialMovement;

class ProductionIn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'production_ins';
    protected $primaryKey = 'id';

    protected $fillable = [
        'production_in_no',
        'date',
        'notes',
        'total_inventory_value',
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
        'total_inventory_value' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(ProductionInItem::class, 'production_in_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Generate unique production in number
    public static function generateProductionInNo(): string
    {
        $prefix = 'PIN-' . now()->format('Ymd');
        $lastRecord = self::where('created_at', '>=', now()->startOfDay())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastRecord ? ((int)substr($lastRecord->production_in_no, -3)) + 1 : 1;
        return $prefix . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    // Calculate total inventory value from items
    public function calculateTotalValue(): void
    {
        $this->total_inventory_value = $this->items()->sum('total_value');
        $this->save();
    }

    // Check if can be approved
    public function canBeApproved(): bool
    {
        return $this->status === 'Pending' && $this->items()->count() > 0;
    }

    // Approve production in
    public function approve(User $user): void
    {
        if ($this->canBeApproved()) {
            $materialUsage = $this->calculateMaterialUsage();

            foreach ($materialUsage as $rawMaterialId => $quantityNeeded) {
                $material = RawMaterial::findOrFail($rawMaterialId);

                if ($material->quantity < $quantityNeeded) {
                    throw new \RuntimeException("Insufficient raw material stock for {$material->material_name}. Current stock: {$material->quantity} {$material->unit}, Required: {$quantityNeeded} {$material->unit}.");
                }
            }

            $this->status = 'Approved';
            $this->approved_by = $user->id;
            $this->approved_date = now();
            $this->save();

            foreach ($this->items()->with('product')->get() as $item) {
                $quantityBefore = $item->product->quantity;
                $item->product->increment('quantity', $item->quantity);
                StockMovementLogger::record(
                    $item->product,
                    $quantityBefore,
                    $item->quantity,
                    'IN',
                    'PRODUCTION IN',
                    $this,
                    null,
                    $user->id
                );
            }

            foreach ($materialUsage as $rawMaterialId => $quantityNeeded) {
                $material = RawMaterial::findOrFail($rawMaterialId);
                $material->decrement('quantity', $quantityNeeded);

                RawMaterialMovement::create([
                    'raw_material_id' => $material->id,
                    'quantity' => -$quantityNeeded,
                    'type' => 'Out',
                    'notes' => "Recipe consumption for Production IN {$this->production_in_no}",
                    'date' => now()->toDateString(),
                ]);
            }
        }
    }

    private function calculateMaterialUsage(): array
    {
        $usage = [];

        foreach ($this->items()->with('product.recipe.ingredients')->get() as $item) {
            $recipe = $item->product->recipe;

            if (!$recipe || !$recipe->is_active) {
                continue;
            }

            foreach ($recipe->ingredients as $ingredient) {
                $requiredQuantity = (float) $ingredient->quantity_per_unit * $item->quantity;
                $usage[$ingredient->raw_material_id] = ($usage[$ingredient->raw_material_id] ?? 0) + $requiredQuantity;
            }
        }

        return array_map(fn ($quantity) => round($quantity, 3), $usage);
    }

    // Reject production in
    public function reject(User $user): void
    {
        if ($this->status === 'Pending') {
            $this->status = 'Rejected';
            $this->save();
        }
    }
}
