#!/usr/bin/env sh
set -eu

# Defaults
: "${MYSQL_HOST:=db}"
: "${MYSQL_PORT:=3306}"
: "${MYSQL_DATABASE:=rss_aggregator}"
: "${MYSQL_USER:=rss}"
: "${MYSQL_PASSWORD:=rss}"
: "${MEMCACHED_HOST:=memcached}"
: "${MEMCACHED_PORT:=11211}"
: "${APP_BASE_URL:=/}"
: "${APP_TIMEZONE:=Europe/Moscow}"
: "${ITEMS_PER_PAGE:=20}"
: "${FEED_URL:=https://ria.ru/export/rss2/archive/index.xml}"

wait_for_tcp() {
  host="$1"; port="$2"; name="$3"; timeout="${4:-60}"
  echo "Waiting for $name at ${host}:${port} (timeout ${timeout}s)..."
  i=0
  while [ $i -lt $timeout ]; do
    if php -r '[$h,$p]=[$argv[1],(int)$argv[2]]; $s=@fsockopen($h,$p,$e,$s,1); if($s){fclose($s); exit(0);} exit(1);' "$host" "$port"; then
      echo "$name is up"
      return 0
    fi
    i=$((i+1))
    sleep 1
  done
  echo "Timeout waiting for $name ($host:$port)" >&2
  return 1
}

generate_config() {
  cat > /var/www/html/config.php <<PHP
<?php
return [
    'db' => [
        'host' => '${MYSQL_HOST}',
        'port' => ${MYSQL_PORT},
        'dbname' => '${MYSQL_DATABASE}',
        'user' => '${MYSQL_USER}',
        'pass' => '${MYSQL_PASSWORD}',
        'charset' => 'utf8mb4',
    ],
    'memcached' => [
        'host' => '${MEMCACHED_HOST}',
        'port' => ${MEMCACHED_PORT},
        'prefix' => 'ria_rss:',
    ],
    'app' => [
        'base_url' => '${APP_BASE_URL}',
        'timezone' => '${APP_TIMEZONE}',
        'items_per_page' => ${ITEMS_PER_PAGE},
    ],
    'feeds' => [
        'ria_archive' => '${FEED_URL}',
    ],
];
PHP
  echo "Generated config.php from environment"
}

apply_schema() {
  if [ -f /var/www/html/db/schema.sql ]; then
    echo "Applying DB schema (idempotent)..."
    mysql -h"${MYSQL_HOST}" -P"${MYSQL_PORT}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" "${MYSQL_DATABASE}" < /var/www/html/db/schema.sql || {
      echo "Warning: failed to apply schema (may be already applied)." >&2
    }
  fi
}

# 1) Wait for DB and Memcached
wait_for_tcp "$MYSQL_HOST" "$MYSQL_PORT" "MySQL" 90
wait_for_tcp "$MEMCACHED_HOST" "$MEMCACHED_PORT" "Memcached" 60

# 2) Generate config
generate_config

# 3) Apply schema
apply_schema

echo "Starting PHP-FPM..."
exec "$@"
