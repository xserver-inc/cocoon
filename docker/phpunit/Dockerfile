ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-cli-bookworm

# PHPUnitとWordPress連携に必要なPHP拡張の導入
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        unzip \
    && docker-php-ext-install -j2 \
        dom \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        xml \
        xmlwriter \
    && rm -rf /var/lib/apt/lists/*

# Composer公式イメージからの実行ファイル取得
COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer

WORKDIR /workspace
