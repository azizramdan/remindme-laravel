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
fi

RUN composer install

RUN if [ -z "$APP_ENV" ]; then php artisan key:generate; fi
RUN php artisan optimize

FROM php:8.2-fpm-alpine as runner

USER 1000
COPY --from=builder /app /app
WORKDIR /app

CMD ["php-fpm"]