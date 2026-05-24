<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $employee_name
 * @property string $employee_email
 * @property string $employee_phone
 * @property string $position
 */
class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'employee_name',
        'employee_email',
        'employee_phone',
        'position',
        'date_hired',
        'emergency_contact',
        'status',
    ];

    protected $casts = [
        'date_hired' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
