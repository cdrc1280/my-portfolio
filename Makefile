.DEFAULT_GOAL := help

DC   := docker compose
EXEC := $(DC) exec -u www-data app
RUN  := $(DC) run --rm --no-deps -u www-data app

help: ## Show this help
	@grep -hE '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

setup: ## First-time setup: env, images, dependencies, key, database
	@test -f .env || cp .env.example .env
	$(DC) build
	$(RUN) composer install
	$(RUN) npm install
	$(RUN) php artisan key:generate
	$(DC) up -d
	$(EXEC) php artisan storage:link
	$(EXEC) php artisan migrate:fresh --seed

up: ## Start the stack (app on :8000, Vite on :5173, Mailpit on :8025)
	$(DC) up -d

down: ## Stop the stack
	$(DC) down

build: ## Rebuild images
	$(DC) build

logs: ## Tail logs from all services
	$(DC) logs -f

shell: ## Open a shell in the app container
	$(EXEC) sh

migrate: ## Run database migrations
	$(EXEC) php artisan migrate

seed: ## Seed the database
	$(EXEC) php artisan db:seed

fresh: ## Drop all tables, migrate and seed
	$(EXEC) php artisan migrate:fresh --seed

test: ## Run the Pest suite
	$(RUN) php artisan test --parallel

lint: ## Check everything: Pint, Larastan, ESLint, Prettier, tsc
	$(RUN) composer lint:check
	$(RUN) composer types:check
	$(RUN) npm run lint:check
	$(RUN) npm run format:check
	$(RUN) npm run types:check

fix: ## Auto-fix code style (Pint, ESLint, Prettier)
	$(RUN) composer lint
	$(RUN) npm run lint
	$(RUN) npm run format

ci: lint test ## Everything CI runs

build-assets: ## Production asset build
	$(RUN) npm run build

.PHONY: help setup up down build logs shell migrate seed fresh test lint fix ci build-assets
