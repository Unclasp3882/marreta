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
        self::saved(fn () => Cache::forget('marreta.blocked_domains'));
        self::deleted(fn () => Cache::forget('marreta.blocked_domains'));
    }
}
