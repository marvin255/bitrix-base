#!/usr/bin/env bash

# default user and default password
CURRENT_USER=${SUDO_USER:-$USER}
PASSWORD='password'

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

# config mysql
echo "[mysqld]" >> /etc/mysql/my.cnf
echo "sql-mode=\"\"" >> /etc/mysql/my.cnf
sudo service mysql restart

# install apache and php
sudo apt-get install -y php5.6 php5.6-cli php5.6-mysql php5.6-gd php5.6-mcrypt php5.6-sqlite php5.6-json php5.6-curl php5.6-mbstring php5.6-xml apache2 libapache2-mod-php5.6
sudo phpenmod mcrypt

# config php.ini
mkdir /var/www/session && chown ${CURRENT_USER}:${CURRENT_USER} /var/www/session

sudo sed -i "s/^\(short_open_tag\).*/\1 = On/" /etc/php/5.6/cli/php.ini
sudo sed -i "s/^;*\(date\.timezone\).*/\1 = 'Europe\/Moscow'/" /etc/php/5.6/cli/php.ini
sudo sed -i "s/^;*\(session\.save_path\).*/\1 = '\/var\/www\/session'/" /etc/php/5.6/cli/php.ini

sudo sed -i "s/^\(short_open_tag\).*/\1 = On/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^;*\(session\.save_path\).*/\1 = '\/var\/www\/session'/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^\(post_max_size\).*/\1 = 500M/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^\(upload_max_filesize\).*/\1 = 500M/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^\(error_reporting\).*/\1 = E_ALL/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^\(display_errors\).*/\1 = On/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^\(display_startup_errors\).*/\1 = On/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^\(html_errors\).*/\1 = On/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^;*\(opcache\.revalidate_freq\).*/\1 = 0/" /etc/php/5.6/apache2/php.ini
sudo sed -i "s/^;*\(date\.timezone\).*/\1 = 'Europe\/Moscow'/" /etc/php/5.6/apache2/php.ini

# install phpmyadmin and give password(s) to installer
# for simplicity I'm using the same password for mysql and phpmyadmin
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt-get -y install phpmyadmin

# setup hosts file
VHOST=$(cat <<EOF
User ${CURRENT_USER}
<VirtualHost *:80>
    DocumentRoot "/var/www/bitrix-base/web"
    <Directory "/var/www/bitrix-base/web">
        DirectorySlash Off
        AllowOverride All
        Options -Indexes
        Require all granted

        php_admin_value mbstring.func_overload 2
        php_value mbstring.internal_encoding utf8
        php_value default_charset utf-8
    </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf

# enable mod_rewrite
sudo a2enmod rewrite

# restart apache
sudo service apache2 restart

# install git
sudo apt-get -y install git

# install Composer
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
