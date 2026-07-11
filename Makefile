.PHONY: setup up down build restart logs backend-bash frontend-bash migrate seed

setup:
	@echo "Building and starting Docker containers..."
	docker compose up -d --build
	@echo "Waiting for the database to be ready..."
	sleep 5
	@echo "Installing backend dependencies..."
	docker compose exec backend composer install
	@echo "Configuring environment files..."
	docker compose exec backend cp -n .env.example .env || true
	docker compose exec backend php artisan key:generate
	@echo "Running migrations and seeders..."
	docker compose exec backend php artisan migrate --seed
	@echo "Project setup complete! 🎉"
	@echo "Frontend is available at: http://localhost:3000"
	@echo "Backend API is available at: http://localhost:8088/api"

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

restart:
	docker compose restart

logs:
	docker compose logs -f

migrate:
	docker compose exec backend php artisan migrate

seed:
	docker compose exec backend php artisan db:seed

test:
	docker compose exec backend php artisan test

backend-bash:
	docker compose exec backend bash

frontend-bash:
	docker compose exec frontend sh
