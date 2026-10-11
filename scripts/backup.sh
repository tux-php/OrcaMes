#!/bin/sh
# Backup de produção: banco (dump) e arquivos enviados (comprovantes do IR).
# Uso: scripts/backup.sh [pasta de destino]   — padrão: /var/backups/orcames
# Guarda os últimos 14 dias. Rode pelo cron, a partir da pasta do projeto.
set -eu

DESTINO="${1:-/var/backups/orcames}"
MANTER_DIAS=14
AGORA="$(date +%Y-%m-%d_%H%M)"
COMPOSE="${ORCAMES_COMPOSE:-docker compose -f docker-compose.producao.yml}"

cd "$(dirname "$0")/.."
mkdir -p "$DESTINO"

$COMPOSE exec -T db sh -c 'exec mysqldump --single-transaction --no-tablespaces -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' \
    | gzip > "$DESTINO/banco_$AGORA.sql.gz"

$COMPOSE exec -T app tar -czf - -C /app/storage/app private > "$DESTINO/arquivos_$AGORA.tar.gz"

# Um dump vazio significa que o mysqldump falhou: não apaga os antigos nesse caso.
if [ "$(gzip -dc "$DESTINO/banco_$AGORA.sql.gz" | wc -c)" -lt 1000 ]; then
    echo "ERRO: o dump do banco saiu vazio; backups antigos mantidos." >&2
    exit 1
fi

find "${DESTINO:?}" -maxdepth 1 -type f \( -name 'banco_*.sql.gz' -o -name 'arquivos_*.tar.gz' \) -mtime "+$MANTER_DIAS" -delete

echo "Backup concluído em $DESTINO ($AGORA)."
