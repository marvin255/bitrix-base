FROM php:7.1-fpm


ARG TIMEZONE
ARG HOST_USER_ID
ARG HOST_GROUP_ID


# packages
RUN apt-get update && apt-get install -y \
    openssl \
    unzip \
    libicu-dev \
    libpng-dev \
    zlib1g-dev \
    libmcrypt-dev \
    libxml2-dev \
    libssl-dev \
    libcurl4-openssl-dev


# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone


# php extensions
RUN docker-php-ext-install pdo pdo_mysql intl gd zip mcrypt json soap curl mbstring xml


# bitrix pool config
COPY bitrix.conf /usr/local/etc/php-fpm.d/bitrix.conf


# permissions settings
RUN if [ ! -z "$HOST_USER_ID" ] ; then usermod -u $HOST_USER_ID www-data ; fi
RUN if [ ! -z "$HOST_GROUP_ID" ] ; then groupmod -g $HOST_GROUP_ID www-data ; fi


WORKDIR /var/www
