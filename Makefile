# Laravel Sail Makefile
# Common commands to manage Docker containers

.PHONY: help up down build restart logs shell mysql redis meilisearch mailpit test fresh install permissions fix-permissions restart-proper

# Default target
help:
	@echo "Available commands:"
	@echo "  make up          - Start all containers"
	@echo "  make down        - Stop all containers"
	@echo "  make build       - Build containers"
	@echo "  make restart     - Restart all containers"
	@echo "  make logs        - Show logs from all containers"
	@echo "  make shell       - Access Laravel container shell"
	@echo "  make mysql       - Access MySQL container shell"
	@echo "  make redis       - Access Redis container shell"
	@echo "  make meilisearch - Access Meilisearch container shell"
	@echo "  make mailpit     - Access Mailpit container shell"
	@echo "  make test        - Run tests"
	@echo "  make fresh       - Fresh install with dependencies"
	@echo "  make install     - Install dependencies"
	@echo "  make migrate     - Run Laravel migrations"
	@echo "  make migrate-seed - Run migrations with seeders"
	@echo "  make migrate-fresh - Fresh migrations with seeders"
	@echo "  make migrate-rollback - Rollback last migration"
	@echo "  make permissions - Fix Laravel storage permissions"
	@echo "  make fix-permissions - Force fix permissions (777)"
	@echo "  make restart-proper - Restart with proper user"

# Start all containers
up:
	docker-compose up -d

# Stop all containers
down:
	docker-compose down

# Build containers
build:
	docker-compose build

# Restart all containers
restart:
	docker-compose restart

# Show logs from all containers
logs:
	docker-compose logs -f

# Access Laravel container shell
shell:
	docker-compose exec laravel.test bash

# Access MySQL container shell
mysql:
	docker-compose exec mysql mysql -u root -p

# Access Redis container shell
redis:
	docker-compose exec redis redis-cli

# Access Meilisearch container shell
meilisearch:
	docker-compose exec meilisearch sh

# Access Mailpit container shell
mailpit:
	docker-compose exec mailpit sh

# Run tests
test:
	docker-compose exec laravel.test php artisan test

# Fresh install with dependencies
fresh:
	docker-compose down -v
	docker-compose up -d
	docker-compose exec laravel.test composer install
	docker-compose exec laravel.test npm install
	docker-compose exec laravel.test php artisan key:generate
	make permissions
	docker-compose exec laravel.test php artisan migrate:fresh --seed

# Install dependencies
install:
	docker-compose exec laravel.test composer install
	docker-compose exec laravel.test npm install

# Run Laravel migrations
migrate:
	docker-compose exec laravel.test php artisan migrate

# Run Laravel migrations with seed
migrate-seed:
	docker-compose exec laravel.test php artisan migrate --seed

# Fresh migrations (drop all tables and re-run)
migrate-fresh:
	docker-compose exec laravel.test php artisan migrate:fresh --seed

# Rollback last migration
migrate-rollback:
	docker-compose exec laravel.test php artisan migrate:rollback

# Fix Laravel permissions
permissions:
	docker-compose exec laravel.test chown -R sail:sail /var/www/html/storage
	docker-compose exec laravel.test chown -R sail:sail /var/www/html/bootstrap/cache
	docker-compose exec laravel.test chmod -R 775 /var/www/html/storage
	docker-compose exec laravel.test chmod -R 775 /var/www/html/bootstrap/cache
	docker-compose exec laravel.test chmod -R 775 /var/www/html/storage/framework
	docker-compose exec laravel.test chmod -R 775 /var/www/html/storage/logs
	docker-compose exec laravel.test chmod -R 775 /var/www/html/storage/app

# Force fix permissions (more aggressive)
fix-permissions:
	docker-compose exec laravel.test chown -R 1000:1000 /var/www/html
	docker-compose exec laravel.test chmod -R 777 /var/www/html/storage
	docker-compose exec laravel.test chmod -R 777 /var/www/html/bootstrap/cache

# Restart with proper user (requires .env file with WWWUSER=1000, WWWGROUP=1000)
restart-proper:
	docker-compose down
	docker-compose up -d
	make fix-permissions

# Show container status
status:
	docker-compose ps

# Clean up (remove containers, networks, and volumes)
clean:
	docker-compose down -v --remove-orphans

# View specific service logs
logs-app:
	docker-compose logs -f laravel.test

logs-mysql:
	docker-compose logs -f mysql

logs-redis:
	docker-compose logs -f redis

logs-meilisearch:
	docker-compose logs -f meilisearch

logs-mailpit:
	docker-compose logs -f mailpit 