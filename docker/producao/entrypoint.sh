#!/bin/sh
set -e

# Só ao subir o servidor (argumentos começando com "-"): comandos avulsos,
# como "php artisan ...", passam direto sem migrar nem refazer caches.
if [ "${1#-}" != "$1" ]; then
    php artisan migrate --force
    php artisan optimize
fi

exec docker-php-entrypoint "$@"
