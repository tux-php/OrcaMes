# Imagem de produção: o código vai dentro da imagem e quem serve é o FrankenPHP
# (Caddy + PHP), que emite e renova o certificado HTTPS sozinho.

FROM composer:2 AS dependencias
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

FROM dunglas/frankenphp:1-php8.4

RUN install-php-extensions pdo_mysql opcache \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/producao/php.ini "$PHP_INI_DIR/conf.d/orcames.ini"

WORKDIR /app
COPY --from=dependencias /app /app

# As pastas de trabalho não vêm no build (.dockerignore); o volume de storage nasce desta estrutura.
RUN mkdir -p storage/app/private storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
    && php artisan package:discover --ansi

COPY docker/producao/entrypoint.sh /usr/local/bin/orcames-entrypoint
RUN chmod +x /usr/local/bin/orcames-entrypoint

ENTRYPOINT ["orcames-entrypoint"]
CMD ["--config", "/etc/frankenphp/Caddyfile", "--adapter", "caddyfile"]
