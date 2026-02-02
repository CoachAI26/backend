@echo off
setlocal enabledelayedexpansion

if "%1"=="" goto help
if "%1"=="help" goto help
if "%1"=="build" goto build
if "%1"=="up" goto up
if "%1"=="down" goto down
if "%1"=="restart" goto restart
if "%1"=="logs" goto logs
if "%1"=="shell" goto shell
if "%1"=="artisan" goto artisan
if "%1"=="migrate" goto migrate
if "%1"=="seed" goto seed
if "%1"=="fresh" goto fresh
if "%1"=="test" goto test
if "%1"=="setup" goto setup
if "%1"=="clean" goto clean
goto help

:help
echo Available commands:
echo   docker build    - Build Docker containers
echo   docker up       - Start Docker containers
echo   docker down     - Stop Docker containers
echo   docker restart  - Restart Docker containers
echo   docker logs     - View container logs
echo   docker shell    - Open shell in app container
echo   docker artisan [cmd]  - Run artisan command
echo   docker migrate  - Run database migrations
echo   docker seed     - Run database seeders
echo   docker fresh    - Fresh migration with seeders
echo   docker test     - Run tests
echo   docker setup    - Initial setup (build, up, migrate, seed)
echo   docker clean    - Clean up containers and volumes
goto end

:build
docker-compose build
goto end

:up
docker-compose up -d
goto end

:down
docker-compose down
goto end

:restart
docker-compose restart
goto end

:logs
docker-compose logs -f
goto end

:shell
docker-compose exec app sh
goto end

:artisan
docker-compose exec app php artisan %2 %3 %4 %5 %6 %7 %8 %9
goto end

:migrate
docker-compose exec app php artisan migrate
goto end

:seed
docker-compose exec app php artisan db:seed
goto end

:fresh
docker-compose exec app php artisan migrate:fresh --seed
goto end

:test
docker-compose exec app php artisan test
goto end

:setup
echo Setting up Docker environment...
if not exist .env (
    echo Copying .env.docker to .env...
    copy .env.docker .env
)
echo Building containers...
docker-compose build
echo Starting containers...
docker-compose up -d
echo Waiting for services to start...
timeout /t 15 /nobreak
echo Generating application key...
docker-compose exec app php artisan key:generate
echo Running migrations and seeders...
docker-compose exec app php artisan migrate --seed
echo.
echo Setup complete! App running at http://localhost:8000
goto end

:clean
docker-compose down -v --remove-orphans
docker system prune -f
goto end

:end
endlocal
