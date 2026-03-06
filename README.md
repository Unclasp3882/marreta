# 🛠️ Marreta

[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/manualdousuario/marreta/blob/master/README.en.md)
[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](https://github.com/manualdousuario/marreta/blob/master/README.md)

[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-purple.svg)](https://www.php.net/)
[![Docker Pulls](https://img.shields.io/docker/pulls/manualdousuario/marreta)](https://hub.docker.com/r/manualdousuario/marreta)

[![Forks](https://img.shields.io/github/forks/manualdousuario/marreta)](https://github.com/manualdousuario/marreta/network/members)
[![Stars](https://img.shields.io/github/stars/manualdousuario/marreta)](https://github.com/manualdousuario/marreta/stargazers)
[![Issues](https://img.shields.io/github/issues/manualdousuario/marreta)](https://github.com/manualdousuario/marreta/issues)

Marreta é uma ferramenta que quebra barreiras de acesso e elementos que atrapalham a leitura!

![Antes e depois do Marreta](https://github.com/manualdousuario/marreta/blob/main/screen.png?raw=true)

Instancia publica em [marreta.pcdomanual.com](https://marreta.pcdomanual.com)!

## ✨ O que tem de legal?

- Limpa e corrige URLs automaticamente
- Remove parâmetros chatos de rastreamento
- Força HTTPS pra manter tudo seguro
- Troca de user agent pra evitar bloqueios
- Deixa o HTML limpinho e otimizado
- Conserta URLs relativas sozinho
- Permite colocar seus próprios estilos e scripts
- Remove elementos indesejados
- Cache, cache!
- Bloqueia domínios que você não quer
- Proteção DMCA com mensagens personalizadas
- Permite configurar headers e cookies do seu jeito
- PHP-FPM e OPcache
- Suporte a Proxy

## 🐳 Instalando em Docker

Instale [Docker e Docker Compose](https://docs.docker.com/engine/install/)

`curl -o ./docker-compose.yml https://raw.githubusercontent.com/manualdousuario/marreta/main/docker-compose.yml`

Agora modifique com suas preferencias:

`nano docker-compose.yml`

- `SITE_NAME`: Nome do seu Marreta
- `SITE_DESCRIPTION`: Conta pra que serve
- `SITE_URL`: Onde vai rodar, endereço completo com `https://`. Se você alterar a porta no docker-compose (ex: 8080:80), você também deve incluir a porta no SITE_URL (ex: https://seusite:8080)
- `SELENIUM_HOST`: Servidor:PORTA do host do Selenium (ex: selenium-hub:4444)
- `LANGUAGE`: pt-br (Português Brasil), en (Inglês), es (Espanhol) ou de-de (Alemão), ru-ru (Russo)
 
Agora só rodar `docker compose up -d`

### Mais configurações:
- Selenium: https://github.com/manualdousuario/marreta/wiki/%F0%9F%92%BB-Selenium-Hub-(Chrome-and-Firefox)
- Cache S3: https://github.com/manualdousuario/marreta/wiki/%F0%9F%97%83%EF%B8%8F-Cache-S3
- Manutenção: https://github.com/manualdousuario/marreta/wiki/%F0%9F%9B%A0%EF%B8%8F-Maintenance

### 🛡️ DMCA

Para bloquear dominios por pedidos de DMCA, crie o arquivo `app/cache/dmca_domains.json`:

```json
[
    {
        "host": "exemplo.com.br",
        "message": "Este conteúdo foi bloqueado a pedido"
    }
]
```

## 🚀 Integrações

- 🤖 **Telegram**: [Bot oficial](https://t.me/leissoai_bot)
- 🦊 **Firefox**: Extensão por [Clarissa Mendes](https://claromes.com/pages/whoami) - [Baixar](https://addons.mozilla.org/pt-BR/firefox/addon/marreta/) | [Código fonte](https://github.com/manualdousuario/marreta-extensao)
- 🌀 **Chrome**: Extensão por [Clarissa Mendes](https://claromes.com/pages/whoami) - [Baixar](https://chromewebstore.google.com/detail/marreta/ipelapagohjgjcgpncpbmaaacemafppe) | [Código fonte](https://github.com/manualdousuario/marreta-extensao)
- 🦋 **Bluesky**: Bot por [Joselito](https://bsky.app/profile/joseli.to) - [Perfil](https://bsky.app/profile/marreta.pcdomanual.com) | [Código fonte](https://github.com/manualdousuario/marreta-bot)
- 🍎 **Apple**: Integração ao [Atalhos](https://www.icloud.com/shortcuts/3594074b69ee4707af52ed78922d624f)

---

Feito com ❤️! Se tiver dúvidas ou sugestões, abre uma issue que a gente ajuda! 😉

Agradecimento ao projeto [Burlesco](https://github.com/burlesco/burlesco) e [Hover](https://github.com/nang-dev/hover-paywalls-browser-extension/) que serviu de base para varias regras!

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=manualdousuario/marreta&type=Date)](https://star-history.com/#manualdousuario/marreta&Date)
