# CodeIgniter 3.1.13 Boilerplate

This project runs CodeIgniter 3.1.13 with Docker, PHP 8.1 FPM, and Nginx, while MySQL runs on the local machine.

## Stack

- PHP 8.1 FPM
- Nginx
- MySQL on the local machine
- CodeIgniter 3.1.13

## Prerequisites

- Docker Desktop or Docker Engine with the Docker Compose plugin
- Port `8080` for the application
- A local MySQL server listening on port `3306`

## Container Structure

- `app`: PHP 8.1 FPM container that runs CodeIgniter
- `nginx`: web server that exposes the application at `http://localhost:8080`

## Database Setup

MySQL is expected to run on the local macOS machine, not inside Docker.

On macOS, the PHP container can reach the host machine through `host.docker.internal`. The current `.env` already uses that host name.

## Environment Configuration

This project reads database configuration from the `.env` file in the project root.

Create `.env` from the example file first:

```bash
cp .env.example .env
```

Use the local MySQL configuration in `.env`:

```env
APP_ENV=development

DB_HOST=host.docker.internal
DB_USER=root
DB_PASS=
DB_NAME=
DB_PORT=
```

Adjust `DB_USER`, `DB_PASS`, `DB_NAME`, and `DB_PORT` to match the MySQL instance installed on the Mac.

## Running the Project

Build and start all services:

```bash
docker compose up -d --build
```

Once the containers are running, the application is available at:

- `http://localhost:8080`
- `http://localhost:8080/health`

The `/health` endpoint returns the application and database connection status.

## Useful Commands

Check container status:

```bash
docker compose ps
```

View application logs:

```bash
docker compose logs -f app nginx
```

Open a shell in the PHP container:

```bash
docker compose exec app bash
```

Stop the services:

```bash
docker compose down
```

## Database Initialization

If you have an SQL dump file, import it into the local MySQL server from the Mac terminal with:

```bash
mysql -h 127.0.0.1 -u root -p ci3_test < dump.sql
```

If there is no database schema yet, the application can still load, but any feature that depends on the database will fail until the tables are created.

## Troubleshooting

If `http://localhost:8080/health` returns database status `DOWN`:

- make sure `.env` uses `DB_HOST=host.docker.internal`
- make sure the `.env` credentials match the local MySQL credentials
- confirm MySQL is running on the Mac and accepting connections on port `3306`
- check logs with `docker compose logs -f app nginx`

If port `3306` conflicts with a local MySQL instance:

- this is expected if MySQL is already installed locally
- keep MySQL on the Mac and leave `DB_HOST=host.docker.internal` with `DB_PORT=3306`

## Notes

- PHP runs from the `php:8.1-fpm` image
- The installed PHP extensions are `mysqli`, `pdo`, and `pdo_mysql`
- Nginx is configured to support CodeIgniter routing through `index.php`
- The Docker setup in this repository does not start a database container