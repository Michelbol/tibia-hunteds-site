# Checklist de Deploy — WebSocket / Reverb

## 1. Pré-deploy: Preparação de Credenciais

- [x] Gerar valores seguros para as credenciais do Reverb:
  - `REVERB_APP_ID` (ex: string aleatória)
  - `REVERB_APP_KEY` (ex: string aleatória)
  - `REVERB_APP_SECRET` (ex: string aleatória)

---

## 2. Atualização do `.env` de Produção

Adicionar ao `.env` do servidor (`src/.env` no host):

- [x] `BROADCAST_CONNECTION=reverb`
- [x] `REVERB_APP_ID=<valor-gerado>`
- [x] `REVERB_APP_KEY=<valor-gerado>`
- [x] `REVERB_APP_SECRET=<valor-gerado>`
- [x] `REVERB_HOST=sitehunteds.michelbolzon.com.br` _(hostname público — world-scraper está em compose separado)_
- [x] `REVERB_PORT=443`
- [x] `REVERB_SCHEME=https`
- [x] `VITE_REVERB_APP_KEY=<mesmo valor de REVERB_APP_KEY>`
- [x] `VITE_REVERB_HOST=sitehunteds.michelbolzon.com.br`
- [x] `VITE_REVERB_PORT=443`
- [x] `VITE_REVERB_SCHEME=https`
- [X] `CACHE_STORE=database` _(o WorldScraper usa Cache para rastrear estado do broadcast)_

---

## 3. Build do Frontend

O `bootstrap.js` agora importa `laravel-echo` e `pusher-js` (novas dependências):

- [x] Rodar `npm install` dentro do container ou no servidor
- [x] Rodar `npm run build` para gerar o bundle em `public/build/`
- [x] Confirmar que `public/build/manifest.json` foi gerado corretamente

> **Atenção:** O `@vite('resources/js/app.js')` é carregado apenas para usuários `super_admin`.
> Se o build não existir, esses usuários verão erro 404 no JS.

---

## 4. Rebuild e Push das Imagens Docker

O `Dockerfile` de `app_web/php` foi alterado (adicionados `pcntl` e Node.js 20):

- [X] Rebuildar a imagem `michelbolzon/tibia-scrapper-app:latest`:
  ```bash
  docker build -t michelbolzon/tibia-scrapper-app:latest ./infra/app_web/php
  docker push michelbolzon/tibia-scrapper-app:latest
  ```
- [x] Confirmar que a extensão `pcntl` está habilitada na nova imagem _(exigida pelo `reverb:start`)_

---

## 5. Deploy do Novo Serviço `reverb`

O serviço `reverb` já está definido em `infra/app_web/docker-compose.yml` mas **nunca foi subido em prod**:

- [x] Subir o container `reverb` no servidor:
  ```bash
  docker compose -f infra/app_web/docker-compose.yml up -d reverb
  ```
- [x] Verificar que o container está `running` e ouvindo na porta 8080 (interna)
- [x] Confirmar que o Nginx faz proxy de `/app` e `/apps` para `reverb:8080` _(já configurado em `default.conf`)_

---

## 6. Atualização do Container `world-scraper`

O `infra/scheduler/docker-compose.yml` foi atualizado com as variáveis do Reverb:

- [X] Restartar o container `world-scraper` para pegar as novas variáveis:
  ```bash
  docker compose -f infra/scheduler/docker-compose.yml up -d --force-recreate world-scraper
  ```
- [X] Confirmar que `REVERB_HOST` aponta para o hostname público (`sitehunteds.michelbolzon.com.br`)
  e **não** para `reverb` _(o scheduler está em compose separado, fora da rede Docker do app)_

---

## 7. Novo Arquivo de Configuração

O `src/config/reverb.php` é um arquivo novo ainda não commitado:

- [X] Commitar e deployar `src/config/reverb.php`
- [X] Rodar `php artisan config:cache` no container `app` após o deploy:
  ```bash
  docker exec <app-container> php artisan config:cache
  ```

---

## 8. Restart dos Containers `app` e `scheduler`

- [X] Restartar o container `app` para carregar a nova imagem e variáveis:
  ```bash
  docker compose -f infra/app_web/docker-compose.yml up -d --force-recreate app
  ```
- [X] Restartar o container `scheduler` (usa a mesma imagem reconstruída):
  ```bash
  docker compose -f infra/app_web/docker-compose.yml up -d --force-recreate scheduler
  ```

---

## 9. Verificação Pós-Deploy

- [ ] Acessar o site e abrir DevTools → Console: verificar o log:
  ```
  [Broadcast] Conectado ao canal online-characters com sucesso.
  ```
- [ ] Verificar no DevTools → Network → WS: conexão WebSocket aberta com o host correto
- [ ] Executar manualmente o scraper e confirmar que o frontend atualiza via WebSocket (sem polling):
  ```bash
  docker exec <world-scraper-container> php artisan world-scraper
  ```
- [ ] Confirmar que o fallback de polling (`startPolling()`) **não** está ativo para usuários `super_admin`

---

## Riscos a Observar

| Item | Risco |
|---|---|
| `REVERB_HOST` no world-scraper | Se apontar para `reverb` (nome interno), o container não alcança o Reverb pois estão em redes Docker separadas. Deve usar o hostname público. |
| `config/reverb.php` não commitado | Se não for deployado, o Reverb não inicializa. |
| Frontend não reconstruído | O `window.Echo` ficará `undefined` e o sistema cai para polling, sem erro visível. |
| `pcntl` ausente na imagem antiga | O `reverb:start` falhará silenciosamente sem a extensão. |
