FROM php:8.2-rc-cli-bookworm

ARG USER=dev

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libmagickwand-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install intl mbstring exif pcntl bcmath

# Install xdebug
RUN pecl install xdebug

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Add scripts directory
COPY .docker/php/scripts /home/$USER/scripts
RUN mkdir /usr/local/etc/php/disabled
RUN chmod -R 777 /usr/local/etc/php
# Override files
COPY .docker/php/files/usr/local/etc/php/disabled/docker-php-ext-xdebug.ini /usr/local/etc/php/disabled/docker-php-ext-xdebug.ini

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer && \
    chown -R $USER:$USER /home/$USER

# Set working directory
WORKDIR /usr/src/app

ENTRYPOINT tail -f /dev/null
