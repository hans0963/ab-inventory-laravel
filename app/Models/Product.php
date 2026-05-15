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
        'expiration_date',
        'stock_alert_threshold'
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

    // Check if product is available for sale
    public function isAvailableForSale(): bool
    {
        $isExpired = $this->expiration_date && now()->isAfter($this->expiration_date);
        return $this->status === 'Active' && !$isExpired && $this->quantity > 0;
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
}
