FROM php:7.3-cli

ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:1.10.10 /usr/bin/composer /usr/bin/composer
RUN set -eux; composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

COPY . /var/www/html
WORKDIR /var/www/html
RUN composer install

CMD "php demo/run.php --port=80"
