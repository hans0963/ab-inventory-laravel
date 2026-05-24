<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $customer_type
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'customer_type',
        'credit_limit',
        'current_balance',
        'credit_due_date',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'credit_due_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function creditPayments()
    {
        return $this->hasMany(CustomerCreditPayment::class);
    }

    public function isCreditCustomer(): bool
    {
        return $this->customer_type === 'Credit/Loan Customer';
    }

    public function availableCredit(): float
    {
        return max(0, (float) $this->credit_limit - (float) $this->current_balance);
    }

    public function getTotalSpentAttribute()
    {
        return $this->total_spent ?? 0;
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->sales_count ?? 0;
    }
}
