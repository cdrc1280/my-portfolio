#!/usr/bin/env bash
# Backs up the database and uploaded files to ~/backups, keeping the last 14 days.
# Schedule daily with:  (crontab -l; echo "30 3 * * * bash $HOME/my-portfolio/scripts/backup.sh") | crontab -
set -euo pipefail

cd "$(dirname "$0")/.."
COMPOSE="docker compose -f docker-compose.prod.yml"
DEST="${BACKUP_DIR:-$HOME/backups}"
STAMP=$(date +%Y-%m-%d_%H%M)
mkdir -p "$DEST"

set -a; . ./.env; set +a

echo "==> Dumping the database"
$COMPOSE exec -T db sh -c 'exec mysqldump --single-transaction --no-tablespaces -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' \
    | gzip > "$DEST/db_$STAMP.sql.gz"

echo "==> Archiving uploads"
$COMPOSE exec -T app tar -czf - -C /var/www/html/storage/app . > "$DEST/storage_$STAMP.tar.gz"

find "$DEST" -name '*.gz' -mtime +14 -delete
echo "Backup written to $DEST"
