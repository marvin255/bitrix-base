FROM php:7.1-fpm


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
    libfreetype6-dev \
    libwebp-dev \
    libjpeg-dev \
    libpng-dev \
    libxpm-dev \
    libcurl4-openssl-dev


# php extensions
RUN docker-php-ext-install pdo mysqli intl zip mcrypt json soap curl mbstring xml opcache


# php extension for gd with free type
RUN docker-php-ext-configure gd --with-gd --with-webp-dir --with-jpeg-dir \
    --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir \
    --enable-gd-native-ttf
RUN docker-php-ext-install gd


# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# change uid for nginx user to avoid problems with host permissions
ARG HOST_USER_ID
ARG HOST_GROUP_ID
RUN if [ ! -z "$HOST_USER_ID" ] ; then usermod -u $HOST_USER_ID www-data ; fi
RUN if [ ! -z "$HOST_GROUP_ID" ] ; then groupmod -g $HOST_GROUP_ID www-data ; fi
RUN if [ ! -z "$HOST_USER_ID" ] ; then mkdir /var/session && chown $HOST_USER_ID:$HOST_GROUP_ID /var/session ; fi


# bitrix pool config
COPY bitrix.conf /usr/local/etc/php-fpm.d/bitrix.conf


WORKDIR /var/www
