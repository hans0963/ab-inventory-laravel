<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_name',
        'category_id',
        'inventory_type',
        'selling_price',
        'quantity',
        'status',
        'unit',
        'expiration_date',
        'expiry_alert_days',
        'stock_alert_threshold',
        'reorder_level',
        'reorder_quantity',
        'default_supplier_id',
        'supplier_unit_price'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Define relationship with Category
    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function defaultSupplier()
    {
        return $this->belongsTo(Supplier::class, 'default_supplier_id');
    }

    public function supplierPrices()
    {
        return $this->hasMany(ProductSupplierPrice::class);
    }

    public function recipe()
    {
        return $this->hasOne(ProductRecipe::class);
    }

    // Check if product is available for sale
    public function isAvailableForSale(): bool
    {
        $isExpired = $this->expiration_date && now()->isAfter($this->expiration_date);
        return $this->status === 'Active' && !$isExpired && !$this->isUnavailableByStockPolicy();
    }

    // Check if product is expired
    public function isExpired(): bool
    {
        return $this->expiration_date && now()->isAfter($this->expiration_date);
    }

    // Check if product is out of stock
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    public function isAtReorderLevel(): bool
    {
        $threshold = $this->reorder_level ?: $this->stock_alert_threshold;
        return $threshold > 0 && $this->quantity <= $threshold;
    }

    public function isUnavailableByStockPolicy(): bool
    {
        if ($this->isOutOfStock()) {
            return true;
        }

        return SystemSetting::bool('block_sales_at_reorder_level', true) && $this->isAtReorderLevel();
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiration_date) {
            return 'Good';
        }

        if ($this->expiration_date->isPast()) {
            return 'Expired';
        }

        return now()->diffInDays($this->expiration_date, false) <= $this->expiry_alert_days
            ? 'Expiring Soon'
            : 'Good';
    }
}
