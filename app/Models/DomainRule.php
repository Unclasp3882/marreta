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
        self::saved(fn () => Cache::forget('marreta.domain_rules'));
        self::deleted(fn () => Cache::forget('marreta.domain_rules'));
    }
}
