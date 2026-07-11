@echo off
echo Building and starting Docker containers...
docker compose up -d --build

echo Waiting for the database to be ready...
timeout /t 5 /nobreak

echo Installing backend dependencies...
docker compose exec backend composer install

echo Fixing storage permissions...
docker compose exec backend chmod -R a+rwX storage bootstrap/cache

echo Configuring environment files...
docker compose exec backend cp -n .env.example .env
docker compose exec backend php artisan key:generate

echo Running migrations and seeders...
docker compose exec backend php artisan migrate --seed

echo Project setup complete! 
echo Frontend is available at: http://localhost:3000
echo Backend API is available at: http://localhost:8088/api
pause
