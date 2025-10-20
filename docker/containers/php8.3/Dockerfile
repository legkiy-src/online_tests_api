FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y --no-install-recommends \
    nano \
    git \
    curl \
    wget \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libpq-dev \
    libxpm-dev \
    libvpx-dev \
    libxml2-dev \
    libfreetype6-dev \
    libwebp-dev \
    gnupg2 \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP с правильной конфигурацией GD для PHP 8.3
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

RUN docker-php-ext-install \
    bcmath \
    gd \
    intl \
    pdo\
    pdo_pgsql \
    pgsql\
    zip \
    xml \
    opcache

# Установка Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get update \
    && apt-get install -y symfony-cli \
    && rm -rf /var/lib/apt/lists/* \
    && symfony server:ca:install

# Копирование конфигурационных файлов
COPY ./php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY ./umask.sh /etc/profile.d/umask.sh
