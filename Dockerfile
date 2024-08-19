FROM php:8.2-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get --allow-releaseinfo-change update
RUN apt-get install -y \
    build-essential \
    libz-dev \
    libonig-dev \
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    zip \
    unzip \
    git \
    curl \
    libmemcached-dev \
    libicu-dev \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg
RUN docker-php-ext-install gd
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN docker-php-ext-install intl
RUN docker-php-ext-enable intl
RUN pecl install redis
RUN pecl install excimer
RUN docker-php-ext-enable redis
#
# don't need memcached...
#
# RUN printf "\n" | pecl install memcached
# RUN docker-php-ext-enable memcached

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Install npm and yarn
RUN apt-get install gcc g++ make
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y build-essential \
    nodejs \
    npm

COPY --chown=root:root docker/supervisor/supervisor.conf /etc/supervisor/conf.d/supervisord.conf

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
RUN ["chmod", "+x", "./start.sh"]
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
# CMD ./start.sh
CMD ["php-fpm"]
