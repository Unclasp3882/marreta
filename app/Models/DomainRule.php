<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class DomainRule extends Model
{
    protected $fillable = ['domain', 'config', 'is_active'];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('marreta.domain_rules'));
        static::deleted(fn () => Cache::forget('marreta.domain_rules'));
    }
}
