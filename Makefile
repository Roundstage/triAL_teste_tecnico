-include .env
export

.PHONY: help up down build restart logs logs-backend logs-frontend \
        shell-backend shell-frontend shell-db \
        artisan composer migrate migrate-fresh seed key test-backend \
        npm

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# ── Docker ────────────────────────────────────────────────────────────────────

up: ## Start all services (detached)
	docker compose up -d

down: ## Stop and remove containers
	docker compose down

build: ## Rebuild all images (no cache)
	docker compose build --no-cache

restart: ## Restart all services
	docker compose restart

logs: ## Tail all service logs
	docker compose logs -f

logs-backend: ## Tail backend logs
	docker compose logs -f backend

logs-frontend: ## Tail frontend logs
	docker compose logs -f frontend

# ── Shells ────────────────────────────────────────────────────────────────────

shell-backend: ## Open shell in backend container
	docker compose exec backend sh

shell-frontend: ## Open shell in frontend container
	docker compose exec frontend sh

shell-db: ## Open psql session
	docker compose exec db psql -U $(DB_USERNAME) -d $(DB_DATABASE)

# ── Backend ───────────────────────────────────────────────────────────────────

artisan: ## Run artisan command  →  make artisan cmd="route:list"
	docker compose exec backend php artisan $(cmd)

composer: ## Run composer command  →  make composer cmd="require pkg"
	docker compose exec backend composer $(cmd)

migrate: ## Run pending migrations
	docker compose exec backend php artisan migrate --no-interaction

migrate-fresh: ## Drop all tables, re-run migrations and seed
	docker compose exec backend php artisan migrate:fresh --seed --no-interaction

seed: ## Run database seeders
	docker compose exec backend php artisan db:seed --no-interaction

key: ## Generate a new Laravel app key
	docker compose exec backend php artisan key:generate --no-interaction

test-backend: ## Run backend test suite
	docker compose exec backend php artisan test --compact

# ── Frontend ──────────────────────────────────────────────────────────────────

npm: ## Run npm command in frontend  →  make npm cmd="install pkg"
	docker compose exec frontend npm $(cmd)
