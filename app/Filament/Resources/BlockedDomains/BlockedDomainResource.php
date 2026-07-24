<?php

namespace App\Filament\Resources\BlockedDomains;

use App\Filament\Resources\BlockedDomains\Pages\ManageBlockedDomains;
use App\Models\BlockedDomain;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlockedDomainResource extends Resource
{
    protected static ?string $model = BlockedDomain::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_group');
    }

    public static function getModelLabel(): string
    {
        return __('admin.blocked_domains.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.blocked_domains.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText(__('admin.blocked_domains.domain_helper'))
                    ->maxLength(255),
                TextInput::make('reason')
                    ->label(__('admin.blocked_domains.reason'))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain')
                    ->label(__('admin.blocked_domains.domain_column'))
                    ->searchable(),
                TextColumn::make('reason')
                    ->label(__('admin.blocked_domains.reason'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBlockedDomains::route('/'),
        ];
    }
}
