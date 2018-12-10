FROM php:7.1-fpm


# packages
RUN apt-get update && apt-get install -y \
    unzip \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    zlib1g-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libxml2-dev \
    git


# php extension for gd with free type
RUN docker-php-ext-configure gd --with-gd --with-jpeg-dir \
    --with-png-dir --with-zlib-dir --with-freetype-dir \
    --enable-gd-native-ttf

# php extensions
RUN docker-php-ext-install gd mysqli zip mcrypt soap opcache sockets


# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# change uid for nginx user to avoid problems with host permissions
ARG HOST_USER_ID
ARG HOST_GROUP_ID
RUN if [ ! -z "$HOST_USER_ID" ] ; then usermod -u $HOST_USER_ID www-data ; fi
RUN if [ ! -z "$HOST_GROUP_ID" ] ; then groupmod -g $HOST_GROUP_ID www-data ; fi
RUN mkdir /var/session && chown www-data:www-data /var/session


# bitrix pool config
COPY bitrix.conf /usr/local/etc/php-fpm.d/bitrix.conf


WORKDIR /var/www
