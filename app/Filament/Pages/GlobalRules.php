<?php

namespace App\Filament\Pages;

use App\Models\GlobalRuleSet;
use BackedEnum;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class GlobalRules extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.global-rules';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('admin.nav_group');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.global_rules.nav_label');
    }

    public function getTitle(): string
    {
        return __('admin.global_rules.title');
    }

    public function mount(): void
    {
        $this->form->fill([
            'config' => GlobalRuleSet::current()?->config ?? [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('config')
                    ->tabs([
                        Tab::make(__('admin.global_rules.tabs.elements'))
                            ->schema([
                                TagsInput::make('config.idElementRemove')
                                    ->label(__('admin.domain_rules.remove_by_id')),
                                TagsInput::make('config.classElementRemove')
                                    ->label(__('admin.domain_rules.remove_by_class')),
                                TagsInput::make('config.classAttrRemove')
                                    ->label(__('admin.domain_rules.remove_class_attr')),
                                TagsInput::make('config.removeElementsByTag')
                                    ->label(__('admin.domain_rules.remove_by_tag')),
                                TagsInput::make('config.scriptTagRemove')
                                    ->label(__('admin.domain_rules.remove_scripts')),
                                TagsInput::make('config.removeCustomAttr')
                                    ->label(__('admin.domain_rules.remove_custom_attr')),
                            ]),
                        Tab::make(__('admin.global_rules.tabs.headers'))
                            ->schema([
                                KeyValue::make('config.headers')
                                    ->label(__('admin.global_rules.custom_headers'))
                                    ->keyLabel(__('admin.domain_rules.header_key'))
                                    ->valueLabel(__('admin.domain_rules.header_value'))
                                    ->reorderable(false),
                            ]),
                        Tab::make(__('admin.global_rules.tabs.code'))
                            ->schema([
                                CodeEditor::make('config.customStyle')
                                    ->label(__('admin.domain_rules.custom_css'))
                                    ->language(Language::Css)
                                    ->columnSpanFull(),
                                CodeEditor::make('config.customCode')
                                    ->label(__('admin.domain_rules.custom_js'))
                                    ->language(Language::JavaScript)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $ruleSet = GlobalRuleSet::current() ?? new GlobalRuleSet();
        $ruleSet->config = $state['config'];
        $ruleSet->save();

        Notification::make()
            ->title(__('admin.global_rules.saved_notification'))
            ->success()
            ->send();
    }
}
