<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function value(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::value($key, $default ? '1' : '0');
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
