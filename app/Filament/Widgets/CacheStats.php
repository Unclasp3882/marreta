<?php

namespace App\Filament\Widgets;

use App\Services\MarretaCacheService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CacheStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                __('admin.dashboard.cache_count'),
                app(MarretaCacheService::class)->getCacheFileCount(),
            ),
        ];
    }
}
