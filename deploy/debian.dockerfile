FROM php:5.6-fpm

RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install -y \
        libpq-dev \
        libmcrypt-dev \
        curl \
    && docker-php-ext-install -j$(nproc) pdo \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install  mbstring

RUN apt-get install nano supervisor git zip -y

RUN apt-get install -y nginx  && \
    rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    build-essential mysql-client openssh-client libmcrypt-dev libpng-dev tzdata zip wget \
    && cp -R /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime \
    && echo "America/Sao_Paulo" > /etc/timezone \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY . /var/www/html
WORKDIR /var/www/html

RUN rm /etc/nginx/sites-enabled/default

COPY ./deploy/deploy.conf /etc/nginx/conf.d/default.conf

RUN mv /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.backup
COPY ./deploy/www.conf /usr/local/etc/php-fpm.d/www.conf

ADD . /var/www/html
WORKDIR /var/www/html

COPY ./deploy/deploy.conf /etc/nginx/conf.d/default.conf

RUN cp /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.backup
COPY ./deploy/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./deploy/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

RUN usermod -a -G www-data root

RUN php -d memory_limit=3800m composer.phar install

RUN chmod +x ./deploy/run

ENTRYPOINT ["./deploy/run"]

EXPOSE 80
