<?php

namespace App\Filament\Resources\DomainRules\Pages;

use App\Filament\Resources\DomainRules\DomainRuleResource;
use App\Filament\Resources\DomainRules\Schemas\DomainRuleForm;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainRule extends CreateRecord
{
    protected static string $resource = DomainRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return DomainRuleForm::fromRepeaterRows($data);
    }
}
