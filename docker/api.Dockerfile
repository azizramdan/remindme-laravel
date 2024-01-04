FROM composer:latest as builder

COPY src /app
RUN chown -R 1000:1000 /app
WORKDIR /app
USER 1000

RUN if [ ! -f .env ]; then \
    cp .env.example .env; \
    sed -i 's/APP_ENV=.*/APP_ENV=production/' .env; \
    sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env; \
    sed -i 's/APP_URL=.*/APP_URL=http:\/\/localhost:8010/' .env; \
    sed -i 's/LOG_CHANNEL=.*/LOG_CHANNEL=daily/' .env; \
    sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env; \
    sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env; \
    sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env; \
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=remindme/' .env; \
    sed -i 's/DB_USERNAME=.*/DB_USERNAME=root/' .env; \
    sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=root/' .env; \
fi

RUN composer install

FROM php:8.2-fpm-alpine as runner

RUN docker-php-ext-install mysqli pdo_mysql

COPY docker/start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

USER 1000
COPY --from=builder /app /app
WORKDIR /app

RUN if [ -z "$APP_KEY" ]; then php artisan key:generate; fi

ENTRYPOINT ["start-container"]