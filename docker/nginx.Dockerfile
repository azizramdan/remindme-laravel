FROM azizramdan/remindme-api as fpm

FROM nginx:alpine

COPY --from=fpm /app /app
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

CMD ["nginx", "-g", "daemon off;"]