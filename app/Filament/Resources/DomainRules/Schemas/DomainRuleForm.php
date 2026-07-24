<?php

namespace App\Filament\Resources\DomainRules\Schemas;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DomainRuleForm
{
    /**
     * Rule types that are excludable from the merged global rule set for a domain.
     *
     * @return array<string, string>
     */
    private static function excludableGlobalRuleTypes(): array
    {
        return [
            'scriptTagRemove' => __('admin.domain_rules.exclude_rule_types.scriptTagRemove'),
            'classElementRemove' => __('admin.domain_rules.exclude_rule_types.classElementRemove'),
            'classAttrRemove' => __('admin.domain_rules.exclude_rule_types.classAttrRemove'),
            'idElementRemove' => __('admin.domain_rules.exclude_rule_types.idElementRemove'),
            'removeElementsByTag' => __('admin.domain_rules.exclude_rule_types.removeElementsByTag'),
            'removeCustomAttr' => __('admin.domain_rules.exclude_rule_types.removeCustomAttr'),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText(__('admin.domain_rules.domain_helper'))
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label(__('admin.domain_rules.is_active'))
                    ->default(true)
                    ->required(),

                Tabs::make('config')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('admin.domain_rules.tabs.fetch'))
                            ->schema([
                                Select::make('config.fetchStrategies')
                                    ->label(__('admin.domain_rules.fetch_strategy'))
                                    ->helperText(__('admin.domain_rules.fetch_strategy_helper'))
                                    ->options([
                                        'fetchContent' => __('admin.domain_rules.fetch_strategy_options.fetchContent'),
                                        'fetchFromWaybackMachine' => __('admin.domain_rules.fetch_strategy_options.fetchFromWaybackMachine'),
                                        'fetchFromSelenium' => __('admin.domain_rules.fetch_strategy_options.fetchFromSelenium'),
                                    ])
                                    ->native(false),
                                Toggle::make('config.proxy')
                                    ->label(__('admin.domain_rules.use_proxy'))
                                    ->helperText(__('admin.domain_rules.use_proxy_helper')),
                                KeyValue::make('config.headers')
                                    ->label(__('admin.domain_rules.custom_headers'))
                                    ->helperText(__('admin.domain_rules.custom_headers_helper'))
                                    ->keyLabel(__('admin.domain_rules.header_key'))
                                    ->valueLabel(__('admin.domain_rules.header_value'))
                                    ->reorderable(false),
                            ]),

                        Tab::make(__('admin.domain_rules.tabs.elements'))
                            ->schema([
                                TagsInput::make('config.idElementRemove')
                                    ->label(__('admin.domain_rules.remove_by_id'))
                                    ->helperText(__('admin.domain_rules.remove_by_id_helper')),
                                TagsInput::make('config.classElementRemove')
                                    ->label(__('admin.domain_rules.remove_by_class'))
                                    ->helperText(__('admin.domain_rules.remove_by_class_helper')),
                                TagsInput::make('config.classAttrRemove')
                                    ->label(__('admin.domain_rules.remove_class_attr'))
                                    ->helperText(__('admin.domain_rules.remove_class_attr_helper')),
                                TagsInput::make('config.removeElementsByTag')
                                    ->label(__('admin.domain_rules.remove_by_tag'))
                                    ->helperText(__('admin.domain_rules.remove_by_tag_helper')),
                                TagsInput::make('config.scriptTagRemove')
                                    ->label(__('admin.domain_rules.remove_scripts'))
                                    ->helperText(__('admin.domain_rules.remove_scripts_helper')),
                                TagsInput::make('config.removeCustomAttr')
                                    ->label(__('admin.domain_rules.remove_custom_attr'))
                                    ->helperText(__('admin.domain_rules.remove_custom_attr_helper')),
                            ]),

                        Tab::make(__('admin.domain_rules.tabs.code'))
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

                        Tab::make(__('admin.domain_rules.tabs.url_and_global'))
                            ->schema([
                                Repeater::make('config.urlMods.query')
                                    ->label(__('admin.domain_rules.url_mods'))
                                    ->schema([
                                        TextInput::make('key')->label(__('admin.domain_rules.url_mods_param'))->required(),
                                        TextInput::make('value')->label(__('admin.domain_rules.url_mods_value'))->required(),
                                    ])
                                    ->addActionLabel(__('admin.domain_rules.url_mods_add'))
                                    ->columns(2)
                                    ->defaultItems(0),
                                Repeater::make('config.excludeGlobalRules')
                                    ->label(__('admin.domain_rules.exclude_global_rules'))
                                    ->schema([
                                        Select::make('rule_type')
                                            ->label(__('admin.domain_rules.exclude_rule_type'))
                                            ->options(self::excludableGlobalRuleTypes())
                                            ->native(false)
                                            ->required(),
                                        TagsInput::make('values')
                                            ->label(__('admin.domain_rules.exclude_values')),
                                    ])
                                    ->addActionLabel(__('admin.domain_rules.exclude_global_rules_add'))
                                    ->columns(2)
                                    ->defaultItems(0),
                            ]),
                    ]),
            ]);
    }

    /**
     * The stored shape is an associative map (ruleType => values); the Repeater
     * needs a list of {rule_type, values} rows. Called once per page load/save
     * (not per-field-hydration) so the transform stays idempotent.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function toRepeaterRows(array $data): array
    {
        if (! isset($data['config']['excludeGlobalRules']) || ! is_array($data['config']['excludeGlobalRules'])) {
            return $data;
        }

        $rows = [];
        foreach ($data['config']['excludeGlobalRules'] as $ruleType => $values) {
            $rows[] = ['rule_type' => $ruleType, 'values' => (array) $values];
        }
        $data['config']['excludeGlobalRules'] = $rows;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function fromRepeaterRows(array $data): array
    {
        if (! isset($data['config']['excludeGlobalRules']) || ! is_array($data['config']['excludeGlobalRules'])) {
            return $data;
        }

        $result = [];
        foreach ($data['config']['excludeGlobalRules'] as $row) {
            if (! empty($row['rule_type'])) {
                $result[$row['rule_type']] = $row['values'] ?? [];
            }
        }
        $data['config']['excludeGlobalRules'] = $result;

        return $data;
    }
}
