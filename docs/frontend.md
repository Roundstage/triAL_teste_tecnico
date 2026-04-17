# Frontend — Documentação Técnica

## Stack

| Tecnologia | Versão |
|---|---|
| Vue | 3 |
| Quasar | 2 |
| TypeScript | 5 |
| Pinia | 3 |
| Axios | 1 |
| vue-router | 5 |
| Playwright | 1 |

---

## Arquitetura

```
src/
├── boot/
│   ├── axios.ts          # Instância Axios + interceptors JWT + redirect 401
│   └── i18n.ts           # Registro do vue-i18n (mensagens em pt-BR)
├── components/
│   └── AuthLayout.vue    # Layout compartilhado entre Login e Cadastro
├── layouts/
│   └── MainLayout.vue    # Layout autenticado (home)
├── pages/
│   ├── LoginPage.vue     # Formulário de login
│   ├── RegisterPage.vue  # Formulário de cadastro
│   ├── IndexPage.vue     # Home — exibe dados do usuário e cronômetro
│   └── ErrorNotFound.vue # Página 404
├── router/
│   ├── routes.ts         # Definição de rotas com meta { requiresAuth, guest }
│   └── index.ts          # Guard de navegação — protege rotas e redireciona
├── services/
│   └── auth.ts           # Funções de API: login, register, logout, me
└── stores/
    ├── auth.ts           # Pinia store de autenticação
    └── index.ts          # Re-exporta stores
```

---

## Rotas

| Path | Meta | Componente | Descrição |
|---|---|---|---|
| `/` | — | redirect | Redireciona para `/login` |
| `/login` | `guest` | `LoginPage` | Apenas para não autenticados |
| `/cadastro` | `guest` | `RegisterPage` | Apenas para não autenticados |
| `/home` | `requiresAuth` | `MainLayout > IndexPage` | Apenas para autenticados |
| `/:catchAll(.*)` | — | `ErrorNotFound` | 404 |

**Guard de navegação** (`router/index.ts`):
- `requiresAuth` sem token → redireciona `/login` com `Notify.create` (aviso Quasar)
- `guest` com token → redireciona `/home`

---

## Autenticação — Fluxo

```
LoginPage / RegisterPage
  → authService.login() / authService.register()
    → POST /api/auth/login  ou  POST /api/auth/register
    → { token, usuario }
      → useAuthStore.login() / .register()
        → token salvo em localStorage + ref reativa
        → usuário salvo em state
        → router.push('/home')

IndexPage (montagem)
  → useAuthStore.fetchMe()
    → GET /api/auth/me
    → atualiza user ref

Logout
  → useAuthStore.logout()
    → POST /api/auth/logout  (invalida token no blacklist)
    → limpa localStorage e state
    → router.push('/login')
```

**Token JWT** — enviado automaticamente via `axios.ts`:
```
Authorization: Bearer <token>
```

Interceptor de resposta: status `401` → limpa token + redireciona `/#/login`.

---

## Pinia Store (`stores/auth.ts`)

| Propriedade | Tipo | Descrição |
|---|---|---|
| `token` | `Ref<string \| null>` | JWT persistido no localStorage |
| `user` | `Ref<AuthUser \| null>` | Dados do usuário autenticado |
| `isAuthenticated` | `ComputedRef<boolean>` | `!!token` |

| Ação | Descrição |
|---|---|
| `login(email, senha)` | Autentica, salva token e usuário |
| `register(dados)` | Registra, salva token e usuário |
| `logout()` | Invalida token na API, limpa estado local |
| `fetchMe()` | Busca dados atualizados do usuário |

---

## Serviço de API (`services/auth.ts`)

Todas as chamadas usam a instância `api` (Axios) configurada em `boot/axios.ts`.

| Método | Endpoint | Entrada | Saída |
|---|---|---|---|
| `login` | `POST /auth/login` | `LoginCredentials` | `AuthResponse` |
| `register` | `POST /auth/register` | `RegisterCredentials` | `AuthResponse` |
| `logout` | `POST /auth/logout` | — | `void` |
| `me` | `GET /auth/me` | — | `AuthUser` |

**Tipos principais:**

```ts
interface AuthUser {
  id: number;
  nome: string;
  email: string;
  telefone: string;
  data_nascimento: string;
  data_expiracao: string | null;
  status: string;
}

interface RegisterCredentials {
  nome: string;
  email: string;
  senha: string;
  senha_confirmation: string;
  telefone: string;
  data_nascimento: string;
}
```

---

## Variáveis de Ambiente

| Variável | Padrão | Descrição |
|---|---|---|
| `VITE_API_BASE_URL` | `http://localhost:8000/api` | URL base da API |

Definida no `docker-compose.yml` para o container frontend e em `.env` local para desenvolvimento fora do Docker.

---

## Testes E2E (Playwright)

```bash
# Dentro do container (via make)
make test-frontend

# Com interface visual
make test-frontend-ui

# Relatório HTML
make test-frontend-report
```

Os testes usam `page.route()` para mockar toda a API — o backend **não precisa** estar rodando.

### Arquivos

| Arquivo | Cobertura |
|---|---|
| `e2e/login.spec.ts` | Sucesso, credenciais inválidas (401), rate limit (429), erro servidor (500), redirect pós-login |
| `e2e/register.spec.ts` | Sucesso, validações frontend, erros backend (422), redirect pós-cadastro |
| `e2e/home.spec.ts` | Guard de rota (sem token), exibição de dados do usuário, logout, 404 |
| `e2e/helpers.ts` | Mocks reutilizáveis: `mockLogin`, `mockRegister`, `mockMe`, `mockLogout` |

### Configuração (`playwright.config.ts`)

- Inicia o dev server automaticamente (`quasar dev`) se não estiver rodando
- `baseURL`: `http://localhost:9000`
- Navegador: Chromium
- `reuseExistingServer: true` — reaproveita servidor já em execução

---

## Desenvolvimento

```bash
# Instalar dependências (dentro do container)
make npm cmd="install"

# Dev server
make npm cmd="run dev"
# ou
make quasar-dev

# Build de produção
make quasar-build

# Linting
make quasar-lint

# Formatação
make quasar-format
```
