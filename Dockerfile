FROM composer:1

RUN apk update && \
    apk add libxml2-dev && \
    docker-php-ext-install soap

COPY src src/
COPY composer.json .

RUN composer install

COPY . .

CMD php -S 0.0.0.0:8000 -t /app/public
