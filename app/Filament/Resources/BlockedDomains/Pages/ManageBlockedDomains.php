<?php

namespace App\Filament\Resources\BlockedDomains\Pages;

use App\Filament\Resources\BlockedDomains\BlockedDomainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBlockedDomains extends ManageRecords
{
    protected static string $resource = BlockedDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
