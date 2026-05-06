<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'email', 'phone', 'address'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
