<?php

namespace App\Filament\Resources\DomainRules;

use App\Filament\Resources\DomainRules\Pages\CreateDomainRule;
use App\Filament\Resources\DomainRules\Pages\EditDomainRule;
use App\Filament\Resources\DomainRules\Pages\ListDomainRules;
use App\Filament\Resources\DomainRules\Schemas\DomainRuleForm;
use App\Filament\Resources\DomainRules\Tables\DomainRulesTable;
use App\Models\DomainRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DomainRuleResource extends Resource
{
    protected static ?string $model = DomainRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_group');
    }

    public static function getModelLabel(): string
    {
        return __('admin.domain_rules.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.domain_rules.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return DomainRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DomainRulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDomainRules::route('/'),
            'create' => CreateDomainRule::route('/create'),
            'edit' => EditDomainRule::route('/{record}/edit'),
        ];
    }
}
