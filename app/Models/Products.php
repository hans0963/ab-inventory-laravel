<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model {
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id';
    public $incrementing = false; // Since `id` is a char, it's not auto-incrementing
    protected $keyType = 'string'; // Ensures Laravel treats `id` as a string

    protected $fillable = [
        'id',
        'product_name',
        'category_id',
        'buying_price',
        'selling_price',
        'quantity',
        'stock_alert_threshold'
    ];

    // Define relationship with Category
    public function category() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
