<?php

namespace App\Filament\Resources\DomainRules\Pages;

use App\Filament\Resources\DomainRules\DomainRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDomainRules extends ListRecords
{
    protected static string $resource = DomainRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
