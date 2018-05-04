#!/usr/bin/env bash

# settings
CURRENT_USER=${SUDO_USER:-$USER}
PASSWORD='password'
DOCUMENT_ROOT='/var/www/bitrix-base/web'
SESSION_FOLDER='/var/www/session'
MY_CNF='/etc/mysql/conf.d/bitrix.cnf'
PHP_CLI='/etc/php/5.6/cli/php.ini'
PHP_WEB='/etc/php/5.6/apache2/php.ini'
DBNAME='bitrix_base'



# add php repository
sudo apt-get -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php

# update / upgrade
sudo apt-get update
sudo apt-get -y upgrade

# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"
sudo apt-get -y install mysql-server

# install apache and php
sudo apt-get install -y php5.6 php5.6-cli php5.6-mysql php5.6-gd php5.6-mcrypt php5.6-sqlite php5.6-json php5.6-zip php5.6-soap php5.6-curl php5.6-mbstring php5.6-xml
sudo apt-get install -y apache2 libapache2-mod-php5.6
sudo phpenmod mcrypt
sudo a2enmod rewrite

# install phpmyadmin and give password(s) to installer
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt-get -y install phpmyadmin

# install git
sudo apt-get -y install git

# install Composer
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer



# create needed dirs for project
mkdir -p "${SESSION_FOLDER}" && chown "${CURRENT_USER}":"${CURRENT_USER}" "${SESSION_FOLDER}"



# config mysql
MY_CNF_CONTENT=$(cat <<EOF
[mysqld]
sql-mode=""
default-time-zone="+03:00"
EOF
)
echo "${MY_CNF_CONTENT}" > "${MY_CNF}"

# config php.ini for cli
sudo sed -i "s/^\(short_open_tag\).*/\1 = On/" "${PHP_CLI}"
sudo sed -i "s/^;*\(date\.timezone\).*/\1 = 'Europe\/Moscow'/" "${PHP_CLI}"
sudo sed -i "s#^;*\(session\.save_path\).*#\1 = '${SESSION_FOLDER}'#" "${PHP_CLI}"

# config php.ini for web
sudo sed -i "s/^\(short_open_tag\).*/\1 = On/" "${PHP_WEB}"
sudo sed -i "s#^;*\(session\.save_path\).*#\1 = '${SESSION_FOLDER}'#" "${PHP_WEB}"
sudo sed -i "s/^\(post_max_size\).*/\1 = 500M/" "${PHP_WEB}"
sudo sed -i "s/^\(upload_max_filesize\).*/\1 = 500M/" "${PHP_WEB}"
sudo sed -i "s/^\(error_reporting\).*/\1 = E_ALL/" "${PHP_WEB}"
sudo sed -i "s/^\(display_errors\).*/\1 = On/" "${PHP_WEB}"
sudo sed -i "s/^\(display_startup_errors\).*/\1 = On/" "${PHP_WEB}"
sudo sed -i "s/^\(html_errors\).*/\1 = On/" "${PHP_WEB}"
sudo sed -i "s/^;*\(opcache\.revalidate_freq\).*/\1 = 0/" "${PHP_WEB}"
sudo sed -i "s/^;*\(date\.timezone\).*/\1 = 'Europe\/Moscow'/" "${PHP_WEB}"
sudo sed -i "s/^;*\s*\(max_input_vars\).*/\1 = 10000/" "${PHP_WEB}"

# config virtual host file
VHOST=$(cat <<EOF
User ${CURRENT_USER}
<VirtualHost *:80>
    DocumentRoot "${DOCUMENT_ROOT}"
    <Directory "${DOCUMENT_ROOT}">
        DirectorySlash Off
        AllowOverride All
        Options -Indexes
        Require all granted
        php_admin_value mbstring.func_overload 2
        php_value default_charset utf-8
    </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf



# restart services after configuring
sudo service apache2 restart
sudo service mysql restart



#create database
echo "CREATE DATABASE IF NOT EXISTS ${DBNAME} CHARACTER SET utf8 COLLATE utf8_unicode_ci;" | mysql -uroot -p"${PASSWORD}"
