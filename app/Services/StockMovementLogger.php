<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovementLog;

class StockMovementLogger
{
    public static function record(
        Product $product,
        int $quantityBefore,
        int $quantityChanged,
        string $type,
        ?string $reason = null,
        mixed $source = null,
        ?int $employeeId = null,
        ?int $userId = null
    ): void {
        StockMovementLog::create([
            'product_id' => $product->id,
            'employee_id' => $employeeId,
            'user_id' => $userId,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
            'movement_date' => now(),
            'quantity_before' => $quantityBefore,
            'quantity_changed' => $quantityChanged,
            'quantity_after' => $quantityBefore + $quantityChanged,
            'movement_type' => $type,
            'reason' => $reason,
        ]);
    }
}
