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
        self::saved(fn () => Cache::forget('marreta.dmca_domains'));
        self::deleted(fn () => Cache::forget('marreta.dmca_domains'));
    }
}
