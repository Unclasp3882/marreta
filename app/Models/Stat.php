<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Stat extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function incrementCounter(string $key, int $by = 1): int
    {
        $stat = static::firstOrCreate(['key' => $key], ['value' => 0]);
        $stat->increment('value', $by);

        return $stat->value;
    }

    public static function getCounter(string $key, int $default = 0): int
    {
        return (int) (static::where('key', $key)->value('value') ?? $default);
    }
}
