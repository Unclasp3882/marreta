<?php

declare(strict_types=1);

return [
    'nav_group' => 'Regras',
    'save' => 'Salvar',

    'domain_rules' => [
        'label' => 'regra de domínio',
        'plural_label' => 'Regras de domínio',
        'domain_helper' => 'Domínio base, sem www e sem esquema (ex: exemplo.com.br)',
        'is_active' => 'Regra ativa',
        'domain_column' => 'Domínio',
        'active_column' => 'Ativa',

        'tabs' => [
            'fetch' => 'Busca',
            'elements' => 'Remoção de elementos',
            'code' => 'Código customizado',
            'url_and_global' => 'URL e regras globais',
        ],

        'fetch_strategy' => 'Estratégia de busca',
        'fetch_strategy_helper' => 'Deixe em branco para usar a cascata padrão (cURL → Wayback → Navegador)',
        'fetch_strategy_options' => [
            'fetchContent' => 'cURL direto',
            'fetchFromWaybackMachine' => 'Wayback Machine',
            'fetchFromSelenium' => 'Navegador (Lightpanda)',
        ],
        'use_proxy' => 'Usar proxy configurado',
        'use_proxy_helper' => 'Requer PROXY_URL configurado no .env',
        'custom_headers' => 'Headers customizados',
        'custom_headers_helper' => 'Sobrescreve os headers padrão da requisição',
        'header_key' => 'Header',
        'header_value' => 'Valor',

        'remove_by_id' => 'Remover por ID',
        'remove_by_id_helper' => 'IDs de elementos HTML a remover',
        'remove_by_class' => 'Remover por classe',
        'remove_by_class_helper' => 'Elementos com estas classes serão removidos inteiramente',
        'remove_class_attr' => 'Remover classe de atributos',
        'remove_class_attr_helper' => 'Remove apenas o nome da classe do atributo, sem remover o elemento',
        'remove_by_tag' => 'Remover por tag HTML',
        'remove_by_tag_helper' => 'Ex: iframe, style',
        'remove_scripts' => 'Remover scripts',
        'remove_scripts_helper' => 'Correspondência parcial no src, href ou conteúdo inline',
        'remove_custom_attr' => 'Remover atributos customizados',
        'remove_custom_attr_helper' => 'Nome do atributo, ou padrão com * (ex: data-*)',

        'custom_css' => 'CSS customizado',
        'custom_js' => 'JavaScript customizado',

        'url_mods' => 'Modificar parâmetros de query da URL antes de buscar',
        'url_mods_add' => 'Adicionar parâmetro',
        'url_mods_param' => 'Parâmetro',
        'url_mods_value' => 'Valor',

        'exclude_global_rules' => 'Excluir regras globais para este domínio',
        'exclude_global_rules_add' => 'Adicionar exclusão',
        'exclude_rule_type' => 'Tipo de regra',
        'exclude_values' => 'Valores a excluir',
        'exclude_rule_types' => [
            'scriptTagRemove' => 'Scripts removidos',
            'classElementRemove' => 'Elementos removidos por classe',
            'classAttrRemove' => 'Classes removidas de atributos',
            'idElementRemove' => 'Elementos removidos por ID',
            'removeElementsByTag' => 'Elementos removidos por tag',
            'removeCustomAttr' => 'Atributos customizados removidos',
        ],
    ],

    'blocked_domains' => [
        'label' => 'domínio bloqueado',
        'plural_label' => 'Domínios bloqueados',
        'domain_helper' => 'Domínio será impedido de ser processado (ex: exemplo.com.br)',
        'reason' => 'Motivo',
        'domain_column' => 'Domínio',
    ],

    'dmca_domains' => [
        'label' => 'domínio DMCA',
        'plural_label' => 'Domínios DMCA',
        'domain_helper' => 'Domínio será bloqueado por solicitação DMCA (ex: exemplo.com.br)',
        'message' => 'Mensagem exibida ao usuário',
        'domain_column' => 'Domínio',
        'message_column' => 'Mensagem',
    ],

    'global_rules' => [
        'nav_label' => 'Regras globais',
        'title' => 'Regras globais',
        'tabs' => [
            'elements' => 'Remoção de elementos',
            'headers' => 'Headers',
            'code' => 'Código customizado',
        ],
        'custom_headers' => 'Headers customizados aplicados a todos os domínios',
        'saved_notification' => 'Regras globais salvas',
    ],

    'dashboard' => [
        'cache_count' => 'Páginas em cache',
    ],
];
