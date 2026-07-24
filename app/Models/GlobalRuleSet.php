<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
