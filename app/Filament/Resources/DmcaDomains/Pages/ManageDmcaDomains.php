<?php

namespace App\Filament\Resources\DmcaDomains\Pages;

use App\Filament\Resources\DmcaDomains\DmcaDomainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDmcaDomains extends ManageRecords
{
    protected static string $resource = DmcaDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
