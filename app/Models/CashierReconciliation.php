<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'user_id',
        'period_type',
        'date_from',
        'date_to',
        'opening_cash',
        'cash_sales_total',
        'non_cash_sales_total',
        'voided_cash_total',
        'total_sales',
        'transaction_count',
        'expected_cash',
        'actual_cash_count',
        'variance',
        'cashier_notes',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'opening_cash' => 'decimal:2',
        'cash_sales_total' => 'decimal:2',
        'non_cash_sales_total' => 'decimal:2',
        'voided_cash_total' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash_count' => 'decimal:2',
        'variance' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
