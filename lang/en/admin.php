<?php

declare(strict_types=1);

return [
    'nav_group' => 'Rules',
    'save' => 'Save',

    'domain_rules' => [
        'label' => 'domain rule',
        'plural_label' => 'Domain rules',
        'domain_helper' => 'Base domain, without www and without scheme (e.g. example.com)',
        'is_active' => 'Active rule',
        'domain_column' => 'Domain',
        'active_column' => 'Active',

        'tabs' => [
            'fetch' => 'Fetching',
            'elements' => 'Element removal',
            'code' => 'Custom code',
            'url_and_global' => 'URL & global rules',
        ],

        'fetch_strategy' => 'Fetch strategy',
        'fetch_strategy_helper' => 'Leave blank to use the default cascade (cURL → Wayback → Browser)',
        'fetch_strategy_options' => [
            'fetchContent' => 'Direct cURL',
            'fetchFromWaybackMachine' => 'Wayback Machine',
            'fetchFromSelenium' => 'Browser (Lightpanda)',
        ],
        'use_proxy' => 'Use configured proxy',
        'use_proxy_helper' => 'Requires PROXY_URL configured in .env',
        'custom_headers' => 'Custom headers',
        'custom_headers_helper' => 'Overrides the default request headers',
        'header_key' => 'Header',
        'header_value' => 'Value',

        'remove_by_id' => 'Remove by ID',
        'remove_by_id_helper' => 'HTML element IDs to remove',
        'remove_by_class' => 'Remove by class',
        'remove_by_class_helper' => 'Elements with these classes will be removed entirely',
        'remove_class_attr' => 'Remove class from attributes',
        'remove_class_attr_helper' => 'Removes only the class name from the attribute, without removing the element',
        'remove_by_tag' => 'Remove by HTML tag',
        'remove_by_tag_helper' => 'E.g.: iframe, style',
        'remove_scripts' => 'Remove scripts',
        'remove_scripts_helper' => 'Partial match on src, href or inline content',
        'remove_custom_attr' => 'Remove custom attributes',
        'remove_custom_attr_helper' => 'Attribute name, or pattern with * (e.g. data-*)',

        'custom_css' => 'Custom CSS',
        'custom_js' => 'Custom JavaScript',

        'url_mods' => 'Modify URL query parameters before fetching',
        'url_mods_add' => 'Add parameter',
        'url_mods_param' => 'Parameter',
        'url_mods_value' => 'Value',

        'exclude_global_rules' => 'Exclude global rules for this domain',
        'exclude_global_rules_add' => 'Add exclusion',
        'exclude_rule_type' => 'Rule type',
        'exclude_values' => 'Values to exclude',
        'exclude_rule_types' => [
            'scriptTagRemove' => 'Removed scripts',
            'classElementRemove' => 'Elements removed by class',
            'classAttrRemove' => 'Classes removed from attributes',
            'idElementRemove' => 'Elements removed by ID',
            'removeElementsByTag' => 'Elements removed by tag',
            'removeCustomAttr' => 'Removed custom attributes',
        ],
    ],

    'blocked_domains' => [
        'label' => 'blocked domain',
        'plural_label' => 'Blocked domains',
        'domain_helper' => 'Domain will be blocked from processing (e.g. example.com)',
        'reason' => 'Reason',
        'domain_column' => 'Domain',
    ],

    'dmca_domains' => [
        'label' => 'DMCA domain',
        'plural_label' => 'DMCA domains',
        'domain_helper' => 'Domain will be blocked due to a DMCA request (e.g. example.com)',
        'message' => 'Message shown to the user',
        'domain_column' => 'Domain',
        'message_column' => 'Message',
    ],

    'global_rules' => [
        'nav_label' => 'Global rules',
        'title' => 'Global rules',
        'tabs' => [
            'elements' => 'Element removal',
            'headers' => 'Headers',
            'code' => 'Custom code',
        ],
        'custom_headers' => 'Custom headers applied to all domains',
        'saved_notification' => 'Global rules saved',
    ],

    'dashboard' => [
        'cache_count' => 'Cached pages',
    ],
];
