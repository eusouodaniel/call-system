FROM php:5.6-fpm

RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN apt-get update \
    && apt-get install -y curl \
    && apt-get -y autoclean

ENV NVM_DIR /usr/local/nvm
ENV NODE_VERSION 9.0.0

RUN curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v0.31.2/install.sh | bash

RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

RUN apt-get update && apt-get install -y git vim mysql-client openssh-server wget libxml2 curl zip unzip\
    && curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony \
    && chmod a+x /usr/local/bin/symfony \
    && npm i -g uglify-js uglifycss \
    && ln -s /usr/lib/node_modules/uglify-js/bin/uglifyjs /usr/local/bin/ \
    && ln -s /usr/lib/node_modules/uglifycss/uglifycss /usr/local/bin/ \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt soap \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd zip
RUN chmod +x /usr/bin/composer

RUN apt-get update \
    && apt-get install -y \
    libxrender1 \
    libfontconfig \
    libxtst6 \
    xz-utils
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install imagick-3.4.1 \
    && docker-php-ext-enable imagick
RUN curl "https://downloads.wkhtmltopdf.org/0.12/0.12.4/wkhtmltox-0.12.4_linux-generic-amd64.tar.xz" -L -o "wkhtmltopdf.tar.xz"
RUN tar Jxvf wkhtmltopdf.tar.xz
RUN mv wkhtmltox/bin/wkhtmltopdf /usr/local/bin/wkhtmltopdf
COPY timezone.ini /usr/local/etc/php/conf.d/timezone.ini
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN composer global require hirak/prestissimo
