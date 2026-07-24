<?php

declare(strict_types=1);

return [
    'nav_group' => 'Правила',
    'save' => 'Сохранить',

    'domain_rules' => [
        'label' => 'правило домена',
        'plural_label' => 'Правила домена',
        'domain_helper' => 'Базовый домен, без www и без схемы (например, example.com)',
        'is_active' => 'Правило активно',
        'domain_column' => 'Домен',
        'active_column' => 'Активно',

        'tabs' => [
            'fetch' => 'Получение',
            'elements' => 'Удаление элементов',
            'code' => 'Пользовательский код',
            'url_and_global' => 'URL и глобальные правила',
        ],

        'fetch_strategy' => 'Стратегия получения',
        'fetch_strategy_helper' => 'Оставьте пустым, чтобы использовать каскад по умолчанию (cURL → Wayback → Браузер)',
        'fetch_strategy_options' => [
            'fetchContent' => 'Прямой cURL',
            'fetchFromWaybackMachine' => 'Wayback Machine',
            'fetchFromSelenium' => 'Браузер (Lightpanda)',
        ],
        'use_proxy' => 'Использовать настроенный прокси',
        'use_proxy_helper' => 'Требуется PROXY_URL, настроенный в .env',
        'custom_headers' => 'Пользовательские заголовки',
        'custom_headers_helper' => 'Переопределяет стандартные заголовки запроса',
        'header_key' => 'Заголовок',
        'header_value' => 'Значение',

        'remove_by_id' => 'Удалить по ID',
        'remove_by_id_helper' => 'ID HTML-элементов для удаления',
        'remove_by_class' => 'Удалить по классу',
        'remove_by_class_helper' => 'Элементы с этими классами будут удалены полностью',
        'remove_class_attr' => 'Удалить класс из атрибутов',
        'remove_class_attr_helper' => 'Удаляет только имя класса из атрибута, не удаляя сам элемент',
        'remove_by_tag' => 'Удалить по HTML-тегу',
        'remove_by_tag_helper' => 'Напр.: iframe, style',
        'remove_scripts' => 'Удалить скрипты',
        'remove_scripts_helper' => 'Частичное совпадение по src, href или встроенному содержимому',
        'remove_custom_attr' => 'Удалить пользовательские атрибуты',
        'remove_custom_attr_helper' => 'Имя атрибута или шаблон с * (например, data-*)',

        'custom_css' => 'Пользовательский CSS',
        'custom_js' => 'Пользовательский JavaScript',

        'url_mods' => 'Изменить параметры запроса URL перед получением',
        'url_mods_add' => 'Добавить параметр',
        'url_mods_param' => 'Параметр',
        'url_mods_value' => 'Значение',

        'exclude_global_rules' => 'Исключить глобальные правила для этого домена',
        'exclude_global_rules_add' => 'Добавить исключение',
        'exclude_rule_type' => 'Тип правила',
        'exclude_values' => 'Значения для исключения',
        'exclude_rule_types' => [
            'scriptTagRemove' => 'Удалённые скрипты',
            'classElementRemove' => 'Элементы, удалённые по классу',
            'classAttrRemove' => 'Классы, удалённые из атрибутов',
            'idElementRemove' => 'Элементы, удалённые по ID',
            'removeElementsByTag' => 'Элементы, удалённые по тегу',
            'removeCustomAttr' => 'Удалённые пользовательские атрибуты',
        ],
    ],

    'blocked_domains' => [
        'label' => 'заблокированный домен',
        'plural_label' => 'Заблокированные домены',
        'domain_helper' => 'Домен будет заблокирован для обработки (например, example.com)',
        'reason' => 'Причина',
        'domain_column' => 'Домен',
    ],

    'dmca_domains' => [
        'label' => 'домен DMCA',
        'plural_label' => 'Домены DMCA',
        'domain_helper' => 'Домен будет заблокирован по запросу DMCA (например, example.com)',
        'message' => 'Сообщение, показываемое пользователю',
        'domain_column' => 'Домен',
        'message_column' => 'Сообщение',
    ],

    'global_rules' => [
        'nav_label' => 'Глобальные правила',
        'title' => 'Глобальные правила',
        'tabs' => [
            'elements' => 'Удаление элементов',
            'headers' => 'Заголовки',
            'code' => 'Пользовательский код',
        ],
        'custom_headers' => 'Пользовательские заголовки, применяемые ко всем доменам',
        'saved_notification' => 'Глобальные правила сохранены',
    ],

    'dashboard' => [
        'cache_count' => 'Страниц в кэше',
    ],
];
