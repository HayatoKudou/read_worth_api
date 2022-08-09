FROM ubuntu

# Basic
## Locale
RUN apt-get update && apt-get install -y locales && rm -rf /var/lib/apt/lists/* \
	&& localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8
ENV LANG en_US.utf8
## Timezone
RUN apt-get update && apt-get install -y tzdata
RUN cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
## Packages
RUN apt-get update && apt-get install -y make git unzip software-properties-common curl tzdata vim

# Nginx
RUN apt-get update && apt-get install -y nginx
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/nginx/app.conf /etc/nginx/sites-enabled/default

# Node.js (yarn)
RUN curl -s https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - \
    && sh -c "echo deb https://deb.nodesource.com/node_16.x jammy main \
> /etc/apt/sources.list.d/nodesource.list" \
    && apt-get update && apt-get install nodejs \
    && npm install --location=global yarn

# PHP
RUN add-apt-repository ppa:ondrej/php && apt-get update \
    && apt-get install --yes php8.1 php8.1-mbstring php8.1-fpm php8.1-dom php8.1-curl php8.1-mysql
RUN ln -s /usr/sbin/php-fpm8.1 /usr/sbin/php-fpm && mkdir /run/php && chmod 777 /run/php
COPY ./docker/php/php-fpm.conf /etc/php/8.1/fpm/php-fpm.conf
COPY ./docker/php/www.conf /etc/php/8.1/fpm/pool.d/www.conf
COPY ./docker/php/app-prod.ini /etc/php/8.1/mods-available/app.ini
RUN ln -s /etc/php/8.1/mods-available/app-prod.ini /etc/php/8.1/cli/conf.d/99-app.ini \
    && ln -s /etc/php/8.1/mods-available/app-prod.ini /etc/php/8.1/fpm/conf.d/99-app.ini

# Composer
ARG COMPOSER_VERSION=2.3.7
RUN curl -sSfLO https://github.com/composer/composer/releases/download/$COMPOSER_VERSION/composer.phar \
    && chmod +x composer.phar \
    && mv composer.phar /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /opt/composer
ENV PATH $PATH:/opt/composer/vendor/bin

# App
COPY ./ /opt/app/
WORKDIR /opt/app
RUN composer install --optimize-autoloader --no-dev --no-scripts \
    && chmod 777 ./bootstrap/cache \
    && find storage -type d | xargs chmod 777
