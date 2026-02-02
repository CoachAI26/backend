# Sean AI Assistance API

A Laravel 12 API for AI-assisted practice sessions and challenges.

## Requirements

- Docker & Docker Compose
- OR PHP 8.2+, Composer, Node.js 20+, MySQL 8.0

## Quick Start with Docker

### Windows

```bash
# First time setup
docker.bat setup

# Start containers
docker.bat up

# Stop containers
docker.bat down
```

### macOS / Linux

```bash
# First time setup
make setup

# Start containers
make up

# Stop containers
make down
```

The API will be available at `http://localhost:8000`

## Docker Commands

| Command | Description |
|---------|-------------|
| `setup` | Initial setup (build, start, migrate, seed) |
| `build` | Build Docker containers |
| `up` | Start containers in background |
| `down` | Stop containers |
| `restart` | Restart containers |
| `logs` | View container logs |
| `shell` | Open shell in app container |
| `migrate` | Run database migrations |
| `seed` | Run database seeders |
| `fresh` | Fresh migration with seeders |
| `test` | Run PHPUnit tests |
| `clean` | Remove all containers and volumes |

### Running Artisan Commands

```bash
# Windows
docker.bat artisan migrate:status
docker.bat artisan tinker

# macOS / Linux
make artisan cmd="migrate:status"
make artisan cmd="tinker"
```

## Services

| Service | Port | Description |
|---------|------|-------------|
| Nginx | 8000 | Web server |
| MySQL | 3306 | Database |
| Redis | 6379 | Cache/Queue |
| MailHog | 8025 | Email testing UI (dev only) |

## Local Development (Without Docker)

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# Start development server
composer dev
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/    # API Controllers
│   │   ├── Requests/           # Form Requests
│   │   └── Resources/          # API Resources
│   └── Models/                 # Eloquent Models
├── database/
│   ├── factories/              # Model Factories
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── docker/                     # Docker configuration
│   ├── nginx/                  # Nginx config
│   ├── php/                    # PHP config
│   └── supervisor/             # Supervisor config
├── routes/
│   └── api.php                 # API Routes
└── tests/                      # PHPUnit Tests
```

## API Authentication

This API uses Laravel Sanctum for authentication. Include the token in requests:

```
Authorization: Bearer {your-token}
```

## Environment Variables

Copy `.env.docker` to `.env` for Docker setup, or `.env.example` for local setup.

Key variables:
- `APP_URL` - Application URL
- `DB_*` - Database configuration
- `REDIS_*` - Redis configuration
- `MAIL_*` - Mail configuration

## Testing

```bash
# Docker
docker.bat test  # Windows
make test        # macOS/Linux

# Local
php artisan test
```

## License

MIT License
