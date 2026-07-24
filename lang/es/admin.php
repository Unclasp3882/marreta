<?php

declare(strict_types=1);

return [
    'nav_group' => 'Reglas',
    'save' => 'Guardar',

    'domain_rules' => [
        'label' => 'regla de dominio',
        'plural_label' => 'Reglas de dominio',
        'domain_helper' => 'Dominio base, sin www y sin esquema (ej: ejemplo.com)',
        'is_active' => 'Regla activa',
        'domain_column' => 'Dominio',
        'active_column' => 'Activa',

        'tabs' => [
            'fetch' => 'Búsqueda',
            'elements' => 'Eliminación de elementos',
            'code' => 'Código personalizado',
            'url_and_global' => 'URL y reglas globales',
        ],

        'fetch_strategy' => 'Estrategia de búsqueda',
        'fetch_strategy_helper' => 'Deje en blanco para usar la cascada predeterminada (cURL → Wayback → Navegador)',
        'fetch_strategy_options' => [
            'fetchContent' => 'cURL directo',
            'fetchFromWaybackMachine' => 'Wayback Machine',
            'fetchFromSelenium' => 'Navegador (Lightpanda)',
        ],
        'use_proxy' => 'Usar proxy configurado',
        'use_proxy_helper' => 'Requiere PROXY_URL configurado en el .env',
        'custom_headers' => 'Headers personalizados',
        'custom_headers_helper' => 'Sobrescribe los headers predeterminados de la solicitud',
        'header_key' => 'Header',
        'header_value' => 'Valor',

        'remove_by_id' => 'Eliminar por ID',
        'remove_by_id_helper' => 'IDs de elementos HTML a eliminar',
        'remove_by_class' => 'Eliminar por clase',
        'remove_by_class_helper' => 'Los elementos con estas clases serán eliminados por completo',
        'remove_class_attr' => 'Eliminar clase de atributos',
        'remove_class_attr_helper' => 'Elimina solo el nombre de la clase del atributo, sin eliminar el elemento',
        'remove_by_tag' => 'Eliminar por etiqueta HTML',
        'remove_by_tag_helper' => 'Ej: iframe, style',
        'remove_scripts' => 'Eliminar scripts',
        'remove_scripts_helper' => 'Coincidencia parcial en src, href o contenido en línea',
        'remove_custom_attr' => 'Eliminar atributos personalizados',
        'remove_custom_attr_helper' => 'Nombre del atributo, o patrón con * (ej: data-*)',

        'custom_css' => 'CSS personalizado',
        'custom_js' => 'JavaScript personalizado',

        'url_mods' => 'Modificar parámetros de query de la URL antes de buscar',
        'url_mods_add' => 'Agregar parámetro',
        'url_mods_param' => 'Parámetro',
        'url_mods_value' => 'Valor',

        'exclude_global_rules' => 'Excluir reglas globales para este dominio',
        'exclude_global_rules_add' => 'Agregar exclusión',
        'exclude_rule_type' => 'Tipo de regla',
        'exclude_values' => 'Valores a excluir',
        'exclude_rule_types' => [
            'scriptTagRemove' => 'Scripts eliminados',
            'classElementRemove' => 'Elementos eliminados por clase',
            'classAttrRemove' => 'Clases eliminadas de atributos',
            'idElementRemove' => 'Elementos eliminados por ID',
            'removeElementsByTag' => 'Elementos eliminados por etiqueta',
            'removeCustomAttr' => 'Atributos personalizados eliminados',
        ],
    ],

    'blocked_domains' => [
        'label' => 'dominio bloqueado',
        'plural_label' => 'Dominios bloqueados',
        'domain_helper' => 'El dominio será bloqueado para su procesamiento (ej: ejemplo.com)',
        'reason' => 'Motivo',
        'domain_column' => 'Dominio',
    ],

    'dmca_domains' => [
        'label' => 'dominio DMCA',
        'plural_label' => 'Dominios DMCA',
        'domain_helper' => 'El dominio será bloqueado por solicitud DMCA (ej: ejemplo.com)',
        'message' => 'Mensaje mostrado al usuario',
        'domain_column' => 'Dominio',
        'message_column' => 'Mensaje',
    ],

    'global_rules' => [
        'nav_label' => 'Reglas globales',
        'title' => 'Reglas globales',
        'tabs' => [
            'elements' => 'Eliminación de elementos',
            'headers' => 'Headers',
            'code' => 'Código personalizado',
        ],
        'custom_headers' => 'Headers personalizados aplicados a todos los dominios',
        'saved_notification' => 'Reglas globales guardadas',
    ],

    'dashboard' => [
        'cache_count' => 'Páginas en caché',
    ],
];
