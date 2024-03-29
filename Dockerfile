FROM roneikunkel/php82-fpm-alpine:latest

COPY . /api.pieam.dev
COPY ./.docker/local.ini /usr/local/etc/php/local.ini

WORKDIR /api.pieam.dev

RUN chmod 777 -R bootstrap/cache
RUN chmod 777 -R storage

CMD sh -c "composer install --no-dev && php artisan migrate && php-fpm"
