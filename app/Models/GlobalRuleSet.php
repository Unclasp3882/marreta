<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

final class GlobalRuleSet extends Model
{
    protected $fillable = ['config'];

    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public static function current(): ?self
    {
        return self::latest('id')->first();
    }

    protected static function booted(): void
    {
        self::saved(fn () => Cache::forget('marreta.global_rules'));
        self::deleted(fn () => Cache::forget('marreta.global_rules'));
    }
}
