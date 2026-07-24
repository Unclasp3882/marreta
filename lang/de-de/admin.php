<?php

declare(strict_types=1);

return [
    'nav_group' => 'Regeln',
    'save' => 'Speichern',

    'domain_rules' => [
        'label' => 'Domain-Regel',
        'plural_label' => 'Domain-Regeln',
        'domain_helper' => 'Basisdomain, ohne www und ohne Schema (z. B. beispiel.de)',
        'is_active' => 'Regel aktiv',
        'domain_column' => 'Domain',
        'active_column' => 'Aktiv',

        'tabs' => [
            'fetch' => 'Abruf',
            'elements' => 'Elemente entfernen',
            'code' => 'Benutzerdefinierter Code',
            'url_and_global' => 'URL & globale Regeln',
        ],

        'fetch_strategy' => 'Abrufstrategie',
        'fetch_strategy_helper' => 'Leer lassen, um die Standardkaskade zu verwenden (cURL → Wayback → Browser)',
        'fetch_strategy_options' => [
            'fetchContent' => 'Direktes cURL',
            'fetchFromWaybackMachine' => 'Wayback Machine',
            'fetchFromSelenium' => 'Browser (Lightpanda)',
        ],
        'use_proxy' => 'Konfigurierten Proxy verwenden',
        'use_proxy_helper' => 'Erfordert PROXY_URL in der .env',
        'custom_headers' => 'Benutzerdefinierte Header',
        'custom_headers_helper' => 'Überschreibt die Standard-Header der Anfrage',
        'header_key' => 'Header',
        'header_value' => 'Wert',

        'remove_by_id' => 'Nach ID entfernen',
        'remove_by_id_helper' => 'IDs von HTML-Elementen, die entfernt werden sollen',
        'remove_by_class' => 'Nach Klasse entfernen',
        'remove_by_class_helper' => 'Elemente mit diesen Klassen werden vollständig entfernt',
        'remove_class_attr' => 'Klasse aus Attributen entfernen',
        'remove_class_attr_helper' => 'Entfernt nur den Klassennamen aus dem Attribut, ohne das Element zu entfernen',
        'remove_by_tag' => 'Nach HTML-Tag entfernen',
        'remove_by_tag_helper' => 'Z. B.: iframe, style',
        'remove_scripts' => 'Skripte entfernen',
        'remove_scripts_helper' => 'Teilweise Übereinstimmung bei src, href oder Inline-Inhalt',
        'remove_custom_attr' => 'Benutzerdefinierte Attribute entfernen',
        'remove_custom_attr_helper' => 'Attributname oder Muster mit * (z. B. data-*)',

        'custom_css' => 'Benutzerdefiniertes CSS',
        'custom_js' => 'Benutzerdefiniertes JavaScript',

        'url_mods' => 'Query-Parameter der URL vor dem Abruf ändern',
        'url_mods_add' => 'Parameter hinzufügen',
        'url_mods_param' => 'Parameter',
        'url_mods_value' => 'Wert',

        'exclude_global_rules' => 'Globale Regeln für diese Domain ausschließen',
        'exclude_global_rules_add' => 'Ausschluss hinzufügen',
        'exclude_rule_type' => 'Regeltyp',
        'exclude_values' => 'Auszuschließende Werte',
        'exclude_rule_types' => [
            'scriptTagRemove' => 'Entfernte Skripte',
            'classElementRemove' => 'Nach Klasse entfernte Elemente',
            'classAttrRemove' => 'Aus Attributen entfernte Klassen',
            'idElementRemove' => 'Nach ID entfernte Elemente',
            'removeElementsByTag' => 'Nach Tag entfernte Elemente',
            'removeCustomAttr' => 'Entfernte benutzerdefinierte Attribute',
        ],
    ],

    'blocked_domains' => [
        'label' => 'blockierte Domain',
        'plural_label' => 'Blockierte Domains',
        'domain_helper' => 'Die Domain wird von der Verarbeitung blockiert (z. B. beispiel.de)',
        'reason' => 'Grund',
        'domain_column' => 'Domain',
    ],

    'dmca_domains' => [
        'label' => 'DMCA-Domain',
        'plural_label' => 'DMCA-Domains',
        'domain_helper' => 'Die Domain wird aufgrund einer DMCA-Anfrage blockiert (z. B. beispiel.de)',
        'message' => 'Dem Benutzer angezeigte Nachricht',
        'domain_column' => 'Domain',
        'message_column' => 'Nachricht',
    ],

    'global_rules' => [
        'nav_label' => 'Globale Regeln',
        'title' => 'Globale Regeln',
        'tabs' => [
            'elements' => 'Elemente entfernen',
            'headers' => 'Header',
            'code' => 'Benutzerdefinierter Code',
        ],
        'custom_headers' => 'Benutzerdefinierte Header, die auf alle Domains angewendet werden',
        'saved_notification' => 'Globale Regeln gespeichert',
    ],

    'dashboard' => [
        'cache_count' => 'Zwischengespeicherte Seiten',
    ],
];
