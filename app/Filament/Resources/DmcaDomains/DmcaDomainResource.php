<?php

namespace App\Filament\Resources\DmcaDomains;

use App\Filament\Resources\DmcaDomains\Pages\ManageDmcaDomains;
use App\Models\DmcaDomain;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DmcaDomainResource extends Resource
{
    protected static ?string $model = DmcaDomain::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_group');
    }

    public static function getModelLabel(): string
    {
        return __('admin.dmca_domains.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.dmca_domains.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('host')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText(__('admin.dmca_domains.domain_helper'))
                    ->maxLength(255),
                Textarea::make('message')
                    ->label(__('admin.dmca_domains.message'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('host')
                    ->label(__('admin.dmca_domains.domain_column'))
                    ->searchable(),
                TextColumn::make('message')
                    ->label(__('admin.dmca_domains.message_column'))
                    ->limit(60)
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
            'index' => ManageDmcaDomains::route('/'),
        ];
    }
}
