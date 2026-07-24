<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class BlockedDomain extends Model
{
    protected $fillable = ['domain', 'reason'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('marreta.blocked_domains'));
        static::deleted(fn () => Cache::forget('marreta.blocked_domains'));
    }
}
