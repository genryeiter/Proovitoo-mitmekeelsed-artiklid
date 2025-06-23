# Multilingual Articles Management System (Laravel)

## Overview
REST API for managing multilingual articles built with Laravel 10, following Service-Oriented Architecture (SOA) principles.

## Prerequisites
- Docker (v20.10+)
- Docker Compose (v2.0+)
- Composer

## Installation

1. Clone repository:
```bash
git clone https://github.com/genryeiter/Proovitoo-mitmekeelsed-artiklid.git
cd Proovitoo-mitmekeelsed-artiklid
```

2. Configure environment:
```bash
cp .env.example .env
```

3. Install dependencies
```bash
composer install
```

4. Install sail in dev environment
```bash
composer require laravel/sail --dev
```

5. Setup sail select mariadb
```bash
php artisan sail:install
```

6. Generate app key:
```bash
./vendor/bin/sail artisan key:generate
```

7. Fill out API_KEY env variable (you can either generate it somewhere or type whatever you want):

8. Run application:
```bash
./vendor/bin/sail up -d
```

9. Run migrations:
```bash
./vendor/bin/sail artisan migrate --seed
```

## API Endpoints
### Authentication

Private endpoints require header:
```http
X-API-KEY: your-secure-key-here
```

| Endpoint | Method | Description 
--- | --- | ---
| `/articles` | GET | List public articles
| `/private/articles` | GET | List all articles
| `/articles/{path}` | GET | Get public article
| `/private/articles/{path}` | GET | Get any article
| `/private/articles` | POST | Create article
| `/private/articles/{id}` | PUT | Update article

## Example Requests
### Create article:
```bash
curl -X POST http://localhost:8000/private/articles \
  -H "X-API-KEY: your-secure-key-here" \
  -H "Content-Type: application/json" \
  -d '{
    "node_type": "article",
    "translations": [
      {
        "language_code": "en",
        "title": "Example",
        "path": "example-path",
        "content": "Article content"
      }
    ]
  }'
```

### Get article:
```bash
curl http://localhost:8000/articles/example-path
```

## Testing
### Run tests with:
```bash
docker-compose exec app php artisan test
```

## Database Schema
### Tables:
 - `articles` - Article metadata
 - `article_translations` - Multilingual content
 - `site_languages` - Supported languages

## Troubleshooting
### Common issues:
 - Check container logs: `docker-compose logs`
 - Ensure ports are available (8000, 3306)
 - Verify database connection in `.env`

### Reset database:
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

## License
### MIT