# triAL — Teste Técnico

Aplicação full-stack com **Laravel 13** (API/backend) e **Quasar/Vue 3** (frontend), orquestrada via Docker Compose.

## Stack

| Camada    | Tecnologia                     |
|-----------|-------------------------------|
| Backend   | PHP 8.3 + Laravel 13           |
| Frontend  | Vue 3 + Quasar 2 + TypeScript  |
| Banco     | PostgreSQL 17                  |
| Infra     | Docker Compose + Make          |

---

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) + Docker Compose v2
- `make`

---

## Como rodar

### 1. Configurar variáveis de ambiente

```bash
cp .env.example .env
```

Edite `.env` se quiser trocar portas ou credenciais do banco. O `APP_KEY` **não precisa** ser preenchido manualmente — o container gera automaticamente.

### 2. Subir os containers

```bash
make up
```

Na primeira execução, o entrypoint de cada container faz automaticamente:

- **Backend:** `composer install` → `key:generate` → `migrate` → `php artisan serve`
- **Frontend:** `npm install` → `quasar dev`

### 3. Acessar

| Serviço  | URL                        |
|----------|----------------------------|
| Frontend | http://localhost:9000       |
| Backend  | http://localhost:8000       |
| Banco    | `localhost:5432` (psql)     |

> As portas podem ser alteradas via `.env` (`FRONTEND_PORT`, `BACKEND_PORT`, `DB_EXPOSED_PORT`).

---

## Comandos disponíveis (`make help`)

```
make up               Sobe todos os serviços em background
make down             Para e remove os containers
make build            Reconstrói as imagens sem cache
make restart          Reinicia os serviços
make logs             Exibe logs de todos os serviços
make logs-backend     Exibe logs do backend
make logs-frontend    Exibe logs do frontend

make shell-backend    Abre shell no container do backend
make shell-frontend   Abre shell no container do frontend
make shell-db         Abre sessão psql no banco

make migrate          Executa migrations pendentes
make migrate-fresh    Recria todo o banco (drop + migrate + seed)
make seed             Executa os seeders
make key              Gera nova APP_KEY do Laravel
make test-backend     Roda a suíte de testes (Pest)

make artisan cmd="…"  Roda qualquer comando artisan
make composer cmd="…" Roda qualquer comando composer
make npm cmd="…"      Roda qualquer comando npm no frontend
```

---

## Como funciona

```
triAL_teste_tecnico/
├── apps/
│   ├── backend/    # Laravel 13 — API + lógica de negócio
│   └── frontend/   # Quasar (Vue 3 + TypeScript) — SPA
├── docker/
│   ├── backend/    # Dockerfile + entrypoint do Laravel
│   └── frontend/   # Dockerfile + entrypoint do Quasar
├── docker-compose.yml
├── Makefile
└── .env.example
```

- O **backend** expõe suas rotas em `http://localhost:8000`. A variável `VITE_API_URL` no frontend aponta para esse endereço.
- O **frontend** roda o servidor de desenvolvimento do Quasar em `http://localhost:9000`.
- O **banco** usa um volume Docker (`postgres_data`) para persistir os dados entre reinicializações.
- As dependências (`vendor`, `node_modules`) ficam em volumes nomeados separados, evitando conflitos com o sistema de arquivos do host.

---

## Testes

```bash
make test-backend
```

Roda a suíte Pest diretamente no container do backend.
