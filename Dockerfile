FROM php:8.2-fpm-alpine

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apk update && apk add --no-cache \
    build-base \
    libpng-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    zip \
    unzip \
    git \
    curl \
    icu-dev \
    supervisor \
    imagemagick-dev \
    nginx \
    nodejs \
    npm \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    python3 \
    py3-pip \
    py3-virtualenv \
    mysql-dev \
    libffi-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg
RUN docker-php-ext-install gd
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl
RUN pecl install redis
RUN pecl install excimer
RUN docker-php-ext-enable redis

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN addgroup -g 1000 www && adduser -u 1000 -G www -h /home/www -s /bin/sh -D www

# Copy supervisor configuration
COPY --chown=root:root docker/supervisor/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# Copy existing application directory contents
COPY . /var/www

# Create and activate virtual environment, then install Python libraries
RUN ["chmod", "+x", "./python/install.sh"]
RUN python/install.sh

# Copy existing application directory permissions
RUN ["chmod", "+x", "./start.sh"]
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
