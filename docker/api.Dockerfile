FROM composer:latest as builder

COPY src /app
RUN chown -R 1000:1000 /app
WORKDIR /app
USER 1000:1000

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
    sed -i 's/CACHE_DRIVER=.*/CACHE_DRIVER=redis/' .env; \
    sed -i 's/QUEUE_CONNECTION=.*/QUEUE_CONNECTION=redis/' .env; \
    sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env; \
    sed -i 's/REDIS_HOST=.*/REDIS_HOST=redis/' .env; \
    sed -i 's/REDIS_PASSWORD=.*/REDIS_PASSWORD=null/' .env; \
    sed -i 's/REDIS_PORT=.*/REDIS_PORT=6379/' .env; \
    sed -i 's/MAIL_HOST=.*/MAIL_HOST=mailpit/' .env; \
    sed -i 's/MAIL_PORT=.*/MAIL_PORT=1025/' .env; \
    sed -i 's/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=remindme@mail.com/' .env; \
    sed -i 's/MAIL_FROM_NAME=.*/MAIL_FROM_NAME=RemindMe/' .env; \
fi

RUN composer install

FROM php:8.2-fpm-alpine as runner

RUN docker-php-ext-install pdo_mysql
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis.so

COPY docker/start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

RUN apk add supercronic
COPY docker/scheduler /etc/crontabs/

RUN apk add supervisor
COPY docker/queue.conf /etc/supervisor/conf.d/queue.conf

RUN addgroup -g 1000 www && adduser -u 1000 -D -G www www

RUN mkdir /app
RUN chown -R www:www /app

USER www

COPY --from=builder /app /app

WORKDIR /app

RUN if [ -z "$APP_KEY" ]; then php artisan key:generate; fi

ENTRYPOINT ["start-container"]