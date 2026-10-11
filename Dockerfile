FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Comprovantes do IR: o padrão do PHP (2 MB) é pouco para um PDF digitalizado.
RUN printf 'upload_max_filesize=6M\npost_max_size=8M\n' > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_HOME=/tmp/composer
WORKDIR /app

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
