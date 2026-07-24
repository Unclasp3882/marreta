<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class DmcaDomain extends Model
{
    protected $fillable = ['host', 'message'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('marreta.dmca_domains'));
        static::deleted(fn () => Cache::forget('marreta.dmca_domains'));
    }
}
