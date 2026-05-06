<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $category_name
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = ['category_name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}

