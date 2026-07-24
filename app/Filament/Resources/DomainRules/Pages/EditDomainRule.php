<?php

namespace App\Filament\Resources\DomainRules\Pages;

use App\Filament\Resources\DomainRules\DomainRuleResource;
use App\Filament\Resources\DomainRules\Schemas\DomainRuleForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDomainRule extends EditRecord
{
    protected static string $resource = DomainRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return DomainRuleForm::toRepeaterRows($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return DomainRuleForm::fromRepeaterRows($data);
    }
}
