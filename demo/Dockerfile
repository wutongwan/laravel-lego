FROM php:7.4-cli

ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer /usr/bin/composer /usr/bin/composer

# install extension
RUN set -eux; \
    docker-php-ext-enable opcache; \
    apt-get update; \
    apt install -y libzip-dev libpng-dev; \
    rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install zip gd;

COPY . /var/www/html
WORKDIR /var/www/html

RUN set -eux; \
    COMPOSER_MEMORY_LIMIT=-1 composer install;

CMD ["/usr/local/bin/php", "demo/run.php", "--host=0.0.0.0", "--port=80"]
