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
        'unit_price',
        'discount_type_id',
        'discount_amount',
        'payment_type',
        'credit_due_date',
        'vat_rate',
        'vat_type',
        'vat_amount',
        'subtotal_amount',
        'total_amount',
        'receipt_number',
        'void_status',
        'void_reason',
        'void_requested_by',
        'void_approved_by',
        'voided_at'
    ];

    protected $casts = [
        'date' => 'date',
        'credit_due_date' => 'date',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'voided_at' => 'datetime',
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

    public function getPaymentModeLabelAttribute(): string
    {
        if (empty($this->payment_type)) {
            return 'Cash';
        }

        return match ($this->payment_type) {
            'E-Wallet' => 'GCash',
            'Credit Card' => 'Card',
            default => $this->payment_type,
        };
    }

    /**
     * Calculate total amount after discount and VAT
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->sold * $this->product->selling_price;
        $afterDiscount = $subtotal - $this->discount_amount;
        $vat = $afterDiscount * ($this->vat_rate / 100);
        return (float)($afterDiscount + $vat);
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
