# Docker Makefile for Sean AI Assistance API
.PHONY: help build up down restart logs shell artisan migrate seed fresh test composer npm

# Default target
help:
	@echo "Available commands:"
	@echo "  make build       - Build Docker containers"
	@echo "  make up          - Start Docker containers"
	@echo "  make down        - Stop Docker containers"
	@echo "  make restart     - Restart Docker containers"
	@echo "  make logs        - View container logs"
	@echo "  make shell       - Open shell in app container"
	@echo "  make artisan     - Run artisan command (usage: make artisan cmd='migrate')"
	@echo "  make migrate     - Run database migrations"
	@echo "  make seed        - Run database seeders"
	@echo "  make fresh       - Fresh migration with seeders"
	@echo "  make test        - Run tests"
	@echo "  make composer    - Run composer command (usage: make composer cmd='install')"
	@echo "  make npm         - Run npm command (usage: make npm cmd='install')"
	@echo "  make setup       - Initial setup (build, up, migrate, seed)"

# Build containers
build:
	docker-compose build

# Start containers
up:
	docker-compose up -d

# Stop containers
down:
	docker-compose down

# Restart containers
restart:
	docker-compose restart

# View logs
logs:
	docker-compose logs -f

# Open shell in app container
shell:
	docker-compose exec app sh

# Run artisan command
artisan:
	docker-compose exec app php artisan $(cmd)

# Run migrations
migrate:
	docker-compose exec app php artisan migrate

# Run seeders
seed:
	docker-compose exec app php artisan db:seed

# Fresh migration with seeders
fresh:
	docker-compose exec app php artisan migrate:fresh --seed

# Run tests
test:
	docker-compose exec app php artisan test

# Run composer command
composer:
	docker-compose exec app composer $(cmd)

# Run npm command
npm:
	docker-compose run --rm node npm $(cmd)

# Initial setup
setup:
	@echo "🚀 Setting up Docker environment..."
	@cp -n .env.docker .env 2>/dev/null || true
	@docker-compose build
	@docker-compose up -d
	@echo "⏳ Waiting for services to start..."
	@sleep 10
	@docker-compose exec app php artisan key:generate
	@docker-compose exec app php artisan migrate --seed
	@echo "✅ Setup complete! App running at http://localhost:8000"

# Clean up everything
clean:
	docker-compose down -v --remove-orphans
	docker system prune -f
