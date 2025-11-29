Требуется корректно заполненный `config.php` (хост, порт, логин/пароль MySQL, имя БД).

#### Запуск в Docker
1. Скопируйте пример переменных окружения и при необходимости отредактируйте:
   ```bash
   cp .env.example .env
   ```
2. Поднимите окружение:
   ```bash
   docker compose up -d --build
   ```
   Сервисы:
   - `web` (Nginx) — http://localhost:8080
   - `app` (PHP-FPM)
   - `db` (MySQL 8) — порт 3306 (локально проброшен)
   - `memcached`
   - `cron` — периодический импорт RSS (по умолчанию каждые 10 минут)
   - `adminer` — http://localhost:8081 (удобно смотреть БД)

3. Первичный импорт (по желанию, можно дождаться cron):
   ```bash
   docker compose exec app php cron/fetch_rss.php
   ```
4. Ключевые переменные:
   - `MYSQL_HOST`, `MYSQL_PORT`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`
   - `MEMCACHED_HOST`, `MEMCACHED_PORT`
   - `APP_BASE_URL`, `APP_TIMEZONE`, `ITEMS_PER_PAGE`
   - `FEED_URL` — URL RSS-ленты

5. Остановка и удаление контейнеров:
   ```bash
   docker compose down
   ```

#### Планировщик (cron)
```
*/10 * * * * /usr/bin/php /path/to/project/cron/fetch_rss.php >> /var/log/rss_import.log 2>&1
```

#### Команды:
```
# Проверить и применить схему (без создания БД)
php bin/setup.php

# Создать БД, если её нет, и применить схему
php bin/setup.php --create-db

# Явно указать путь к схеме
php bin/setup.php --schema=db/schema.sql
```

