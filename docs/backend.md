# Backend — Documentação Técnica

## Stack

| Tecnologia | Versão |
|---|---|
| PHP | 8.3 |
| Laravel | 13 |
| tymon/jwt-auth | 2.3 |
| PostgreSQL | 17 |

---

## Arquitetura

```
app/
├── Enums/
│   └── EnumStatusUsuario.php     # ativo | expirado
├── Http/
│   ├── Controllers/Api/
│   │   └── AuthController.php    # Apenas HTTP — delega ao service
│   └── Requests/Api/
│       ├── RegisterRequest.php
│       └── LoginRequest.php
├── Jobs/
│   └── ExpirarUsuariosJob.php    # Desacoplado — injeta o service
├── Models/
│   └── Usuario.php               # JWTSubject, casts, sem User.php
├── Rules/
│   ├── TelefoneRule.php          # Valida formato BR + DDD
│   └── DataNascimentoRule.php    # Idade entre 1 e 120 anos
└── Services/
    ├── AuthService.php           # register, login, logout
    └── ExpirarUsuariosService.php # executar() — expira e loga
```

---

## Tabela `usuarios`

| Coluna | Tipo | Regras |
|---|---|---|
| `id` | bigint | PK |
| `nome` | string | obrigatório |
| `email` | string | único, RFC + DNS |
| `senha` | string | min 6, hashed |
| `telefone` | string | formato BR, DDD válido |
| `data_nascimento` | date | idade 1–120 anos |
| `status` | enum | `ativo` \| `expirado` |
| `data_expiracao` | date | cast automático |
| `created_at` / `updated_at` | timestamp | automático |

---

## Autenticação — Fluxo JWT

```
POST /api/auth/register
  → Valida RegisterRequest (email:rfc,dns, TelefoneRule, DataNascimentoRule)
  → AuthService::registrar() — cria usuário, status=ativo, expiracao=+7d
  → JWTAuth::fromUser() → { token, usuario }

POST /api/auth/login
  → Valida LoginRequest
  → AuthService::autenticar() — attempt() com mapeamento senha→password
  → { token, usuario } ou 401

POST /api/auth/logout  [auth:api]
  → AuthService::encerrarSessao() — invalida token no blacklist

GET  /api/auth/me     [auth:api]
  → Retorna usuário autenticado
```

**Header para rotas protegidas:**
```
Authorization: Bearer <token>
```

---

## Expiração de Usuários

Pipeline agendado diariamente à meia-noite:

```
Scheduler (bootstrap/app.php, 00:00)
  → ExpirarUsuariosJob (queue)
    → ExpirarUsuariosService::executar()
      → UPDATE usuarios SET status='expirado'
         WHERE status='ativo' AND data_expiracao < hoje
      → Log::info('[2026-04-15] ExpirarUsuariosJob: 5 usuários expirados')
```

Para disparar manualmente:
```bash
make artisan cmd="schedule:run"
# ou
make artisan cmd="queue:work --once"
```

---

## Variáveis de Ambiente JWT

Configuradas no `.env` raiz e injetadas via `docker-compose.yml`:

| Variável | Padrão | Descrição |
|---|---|---|
| `JWT_SECRET` | — | Chave de assinatura (gerar com `make artisan cmd="jwt:secret"`) |
| `JWT_ALGO` | `HS256` | Algoritmo de assinatura |
| `JWT_TTL` | `60` | Expiração do access token (minutos) |
| `JWT_REFRESH_TTL` | `20160` | Janela de refresh (14 dias, em minutos) |
| `JWT_BLACKLIST_ENABLED` | `true` | Revogação de tokens no logout |
| `JWT_BLACKLIST_GRACE_PERIOD` | `0` | Tolerância para requests paralelos |

---

## Testes

```bash
make test-backend
```

| Arquivo | Cobertura |
|---|---|
| `Feature/Auth/RegisterTest` | Sucesso, validações, duplicata, campos ocultos |
| `Feature/Auth/LoginTest` | Sucesso, credenciais inválidas, campos |
| `Feature/Auth/LogoutTest` | Sucesso, sem autenticação |
| `Feature/Auth/MeTest` | Dados do usuário, proteção, campos ocultos |
| `Feature/Services/ExpirarUsuariosServiceTest` | Expiração seletiva, contagem, casos extremos |
| `Unit/Rules/TelefoneRuleTest` | Formatos BR, DDI, erros |
| `Unit/Rules/DataNascimentoRuleTest` | Limites de idade |
