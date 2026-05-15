<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'employee_id',
        'customer_id',
        'date',
        'sold',
        'discount_type_id',
        'discount_amount',
        'payment_type',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'receipt_number'
    ];

    protected $casts = [
        'date' => 'date',
        'discount_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function discountType()
    {
        return $this->belongsTo(DiscountType::class, 'discount_type_id');
    }

    /**
     * Calculate total amount after discount and VAT
     */
    public function calculateTotal(): decimal
    {
        $subtotal = $this->sold * $this->product->selling_price;
        $afterDiscount = $subtotal - $this->discount_amount;
        $vat = $afterDiscount * ($this->vat_rate / 100);
        return $afterDiscount + $vat;
    }

    /**
     * Generate receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return "RCP-{$timestamp}-{$random}";
    }
}

