FROM composer:latest AS builder

WORKDIR /app
COPY . .

ARG APP_ENV=production

RUN if [ "$APP_ENV" = "production" ]; then \
        composer install --no-dev --optimize-autoloader; \
    else \
        composer install; \
    fi

FROM php:8.1-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

COPY --from=builder /app /var/www/html

EXPOSE 80