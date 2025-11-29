FROM php:8.4-fpm-alpine

# Install system dependencies and PHP extensions
RUN set -eux; \
    apk add --no-cache \
      git bash curl tzdata \
      libxml2-dev oniguruma-dev \
      icu-data-full icu-dev \
      libzip-dev zip unzip \
      $PHPIZE_DEPS \
      mysql-client \
      libmemcached-dev cyrus-sasl-dev; \
    docker-php-ext-install -j$(nproc) pdo_mysql mbstring simplexml; \
    pecl install memcached; \
    docker-php-ext-enable memcached; \
    rm -rf /tmp/pear

# Opcache (optional, good defaults)
RUN docker-php-ext-install opcache && \
    { \
      echo 'opcache.enable=1'; \
      echo 'opcache.enable_cli=1'; \
      echo 'opcache.validate_timestamps=1'; \
      echo 'opcache.revalidate_freq=2'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copy entrypoints and nginx config directory structure
COPY docker/app/entrypoint.sh /usr/local/bin/app-entrypoint.sh
COPY docker/app/cron-entrypoint.sh /usr/local/bin/cron-entrypoint.sh
RUN chmod +x /usr/local/bin/app-entrypoint.sh /usr/local/bin/cron-entrypoint.sh

# Expose php-fpm on 0.0.0.0:9000 for nginx container
RUN set -eux; \
    sed -ri 's/^;?listen\s*=\s*.*/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf; \
    sed -ri 's/^;?listen\.allowed_clients\s*=.*/; listen.allowed_clients = 127.0.0.1/' /usr/local/etc/php-fpm.d/www.conf

# Default command for app service (overridden for cron service)
ENTRYPOINT ["/usr/local/bin/app-entrypoint.sh"]
CMD ["php-fpm"]
