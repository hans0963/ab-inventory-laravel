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
        'employee_name',
        'employee_email',
        'employee_phone',
        'position',
    ];
}
