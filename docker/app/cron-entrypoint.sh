#!/usr/bin/env sh
set -eu

: "${CRON_SCHEDULE:=*/10 * * * *}"

/usr/local/bin/app-entrypoint.sh true || true

CRON_FILE=/var/spool/cron/crontabs/root
mkdir -p "$(dirname "$CRON_FILE")"
{
  echo "SHELL=/bin/sh"
  echo "PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"
  echo "$CRON_SCHEDULE php /var/www/html/cron/fetch_rss.php >> /proc/1/fd/1 2>&1"
} > "$CRON_FILE"
chmod 600 "$CRON_FILE"

echo "Starting crond with schedule: $CRON_SCHEDULE"
exec crond -f -l 8
