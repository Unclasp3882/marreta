# 🛠️ Marreta

[![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](https://github.com/manualdousuario/marreta/blob/master/README.md)
[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/manualdousuario/marreta/blob/master/README.en.md)

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
- Permite configurar headers e cookies do seu jeito
- PHP-FPM e OPcache

## 🐳 Instalando em Docker

Instale Docker e Docker Compose

`curl -o ./docker-compose.yml https://raw.githubusercontent.com/manualdousuario/marreta/main/docker-compose.yml`

Agora modifique com suas configurações:

`nano docker-compose.yml`

```
services:
  marreta:
    container_name: marreta
    image: ghcr.io/manualdousuario/marreta:latest
    ports:
      - "80:80"
    environment:
      - SITE_NAME=
      - SITE_DESCRIPTION=
      - SITE_URL=
```

- `SITE_NAME`: Nome do seu Marreta
- `SITE_DESCRIPTION`: Conta pra que serve
- `SITE_URL`: Onde vai rodar, endereço completo com `https://`. Se você alterar a porta no docker-compose (ex: 8080:80), você também deve incluir a porta no SITE_URL (ex: https://seusite:8080)
- `DNS_SERVERS`: Quais servidores DNS usar `1.1.1.1, 8.8.8.8`
- `SELENIUM_HOST`: Servidor:PORTA do host do Selenium (ex: selenium-hub:4444)
- 
Agora pode rodar `docker compose up -d`

### Cache S3

Suporte de armazenamento do cache em S3. Configure as seguintes variáveis no seu `.env`:

```env
S3_CACHE_ENABLED=true

S3_ACCESS_KEY=access_key
S3_SECRET_KEY=secret_key
S3_BUCKET=nome_do_bucket
S3_REGION=us-east-1
S3_FOLDER_=cache/
S3_ACL=private
S3_ENDPOINT=
```

Configurações possiveis:

```
## R2
S3_ACCESS_KEY=access_key
S3_SECRET_KEY=secret_key
S3_BUCKET=nome_do_bucket
S3_ENDPOINT=https://{TOKEN}.r2.cloudflarestorage.com
S3_REGION=auto
S3_FOLDER_=cache/
S3_ACL=private

## DigitalOcean
S3_ACCESS_KEY=access_key
S3_SECRET_KEY=secret_key
S3_BUCKET=nome_do_bucket
S3_ENDPOINT=https://{REGIAO}.digitaloceanspaces.com
S3_REGION=auto
S3_FOLDER_=cache/
S3_ACL=private
```

### Integração com Selenium

Integração com Selenium permite processar sites que requerem javascript ou têm algumas barreiras de proteção mais avançadas. Para usar esta funcionalidade, você precisa configurar um ambiente Selenium com Firefox. Adicione a seguinte configuração ao seu `docker-compose.yml`:

```yaml
services:
  selenium-firefox:
    container_name: selenium-firefox
    image: selenium/node-firefox:4.27.0-20241204
    shm_size: 2gb
    environment:
      - SE_EVENT_BUS_HOST=selenium-hub
      - SE_EVENT_BUS_PUBLISH_PORT=4442
      - SE_EVENT_BUS_SUBSCRIBE_PORT=4443
      - SE_ENABLE_TRACING=false
      - SE_NODE_MAX_SESSIONS=10
      - SE_NODE_OVERRIDE_MAX_SESSIONS=true
    entrypoint: bash -c 'SE_OPTS="--host $$HOSTNAME" /opt/bin/entry_point.sh'
    depends_on:
      - selenium-hub

  selenium-hub:
    image: selenium/hub:4.27.0-20241204
    container_name: selenium-hub
    environment:
      - SE_ENABLE_TRACING=false
      - GRID_MAX_SESSION=10
      - GRID_BROWSER_TIMEOUT=10
      - GRID_TIMEOUT=10
    ports:
      - 4442:4442
      - 4443:4443
      - 4444:4444
```

Configurações importantes:
- `shm_size`: Define o tamanho da memória compartilhada para o Firefox (2GB recomendado)
- `SE_NODE_MAX_SESSIONS`: Número máximo de sessões simultâneas por nó
- `GRID_MAX_SESSION`: Número máximo de sessões simultâneas no hub
- `GRID_BROWSER_TIMEOUT` e `GRID_TIMEOUT`: Timeouts em segundos

Após configurar o Selenium, certifique-se de definir a variável `SELENIUM_HOST` no seu ambiente para apontar para o hub do Selenium (geralmente `selenium-hub:4444`).

## Desenvolvimento

1. Primeiro, clone o projeto:
```bash 
git clone https://github.com/manualdousuario/marreta/
cd marreta/app
```

2. Instale as dependências do projeto:
```bash
composer install
npm install
```

3. Cria o arquivo de configuração: 
```bash
cp .env.sample .env
```

4. Configure as variáveis de ambiente no `.env`

5. Utilize o `default.conf` como base do NGINX ou aponte seu webservice para `app/`

O Gulp é usado para compilar Sass para CSS, minificar JavaScript, utilize: `gulp`

### ⚙️ Personalizando

As configurações estão organizadas em `data/`:

- `domain_rules.php`: Regras específicas para cada site
- `global_rules.php`: Regras que se aplicam a todos os sites
- `blocked_domains.php`: Lista de sites bloqueados

### Traduções

- `/languages/`: Cada lingua está em seu ISO id (`pt-br, en, es ou de-de`) e pode ser definida no environment `LANGUAGE`

## 🛠️ Manutenção

### Sistema de Logs

Os logs são armazenados em `app/logs/*.log` com rotação automática a cada 7 dias.

Configurações de log disponíveis no `.env` ou docker:

```env
LOG_LEVEL=WARNING
```

Níveis de log disponíveis:
- DEBUG: Informações detalhadas para debug
- INFO: Informações gerais sobre operações
- WARNING: Avisos que merecem atenção (padrão)
- ERROR: Erros que não interrompem a operação
- CRITICAL: Erros críticos que precisam de atenção imediata

Ver os logs da aplicação:
```bash
docker-compose logs app
# ou diretamente do arquivo de log
cat app/logs/*.log
```

### Limpando o cache

Quando precisar limpar:
```bash
docker-compose exec app rm -rf /app/cache/*
```

## 🚀 Integrações

- 🤖 **Telegram**: [Bot oficial](https://t.me/leissoai_bot)
- 🦊 **Firefox**: Extensão por [Clarissa Mendes](https://claromes.com/pages/whoami) - [Baixar](https://addons.mozilla.org/pt-BR/firefox/addon/marreta/) | [Código fonte](https://github.com/manualdousuario/marreta-extensao)
- 🌀 **Chrome**: Extensão por [Clarissa Mendes](https://claromes.com/pages/whoami) - [Baixar](https://chromewebstore.google.com/detail/marreta/ipelapagohjgjcgpncpbmaaacemafppe) | [Código fonte](https://github.com/manualdousuario/marreta-extensao)
- 🦋 **Bluesky**: Bot por [Joselito](https://bsky.app/profile/joseli.to) - [Perfil](https://bsky.app/profile/marreta.pcdomanual.com) | [Código fonte](https://github.com/manualdousuario/marreta-bot)
- 🍎 **Apple**: Integração ao [Atalhos](https://www.icloud.com/shortcuts/3594074b69ee4707af52ed78922d624f)

---

Feito com ❤️! Se tiver dúvidas ou sugestões, abre uma issue que a gente ajuda! 😉

Agradecimento ao projeto [https://github.com/burlesco/burlesco](Burlesco) e [https://github.com/nang-dev/hover-paywalls-browser-extension/](Hover) que serviu de base para varias regras!

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=manualdousuario/marreta&type=Date)](https://star-history.com/#manualdousuario/marreta&Date)
