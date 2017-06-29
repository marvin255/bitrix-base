#!/usr/bin/env bash

# Use single quotes instead of double quotes to make it work with special-character passwords
PASSWORD='password'

# update / upgrade
sudo apt-get update
sudo apt-get -y upgrade

# install apache 2.5 and php 5.5
sudo apt-get install -y apache2
sudo apt-get install -y php5 php5-gd php5-mcrypt php5-sqlite php5-json php5-curl
sudo php5enmod mcrypt

# install mysql and give password to installer
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"
sudo apt-get -y install mysql-server
sudo apt-get install php5-mysql

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
User vagrant
<VirtualHost *:80>
    DocumentRoot "/var/www/bitrix-base/web"
    <Directory "/var/www/bitrix-base/web">
        DirectorySlash Off
        AllowOverride All
        Options -Indexes
        Require all granted

        php_value short_open_tag 1
        php_value default_charset utf8
        php_admin_value mbstring.func_overload 2
        php_value mbstring.internal_encoding utf8
        php_value error_reporting E_ALL
        php_value display_errors On
        php_value display_startup_errors On
        php_value html_errors On
    </Directory>
</VirtualHost>
EOF
)
echo "${VHOST}" > /etc/apache2/sites-available/000-default.conf

# config php.ini
sudo sed -i "s/^\(short_open_tag\).*/\1 = On/" /etc/php5/cli/php.ini
sudo sed -i "s/^\(post_max_size\).*/\1 = 500M/" /etc/php5/apache2/php.ini
sudo sed -i "s/^\(upload_max_filesize\).*/\1 = 500M/" /etc/php5/apache2/php.ini

# enable mod_rewrite
sudo a2enmod rewrite

# restart apache
sudo service apache2 restart

# install git
sudo apt-get -y install git

# install Composer
curl -s https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
