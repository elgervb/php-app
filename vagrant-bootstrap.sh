#!/usr/bin/env bash

# Variables
APPENV=local
DBHOST=localhost
DBNAME=dbname
DBUSER=dbuser
DBPASSWD=test123

sudo apt-get -qq update


# install base packages
echo -e "\n--- Install base packages ---\n"
# sudo apt-get -y install curl vim git build-essentials python-software-properties > /dev/null 2>&1
sudo apt-get -y install curl vim git python-software-properties > /dev/null 2>&1


# install Apache and enable mod-rewrite
echo -e "\n--- Install Apache webserver ---\n"
sudo apt-get install -y apache2
sudo a2enmod rewrite > /dev/null 2>&1
sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
echo "ServerName localhost" | sudo tee /etc/apache2/conf.d/fqdn
if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant/src /var/www
fi

# enable ssl
sudo make-ssl-cert generate-default-snakeoil --force-overwrite
sudo a2enmod ssl
sudo a2enmod headers

sudo a2ensite default-ssl

# allow override all
sudo sed -i "s/AllowOverride .*/AllowOverride All/" /etc/apache2/sites-available/default
sudo sed -i "s/AllowOverride .*/AllowOverride All/" /etc/apache2/sites-available/default-ssl
sudo service apache2 restart


# install PHP 
echo -e "\n--- Install PHP ---\n"
add-apt-repository ppa:ondrej/php5 > /dev/null 2>&1
apt-get -y install php5 apache2 libapache2-mod-php5 php5-curl php5-gd php5-mcrypt php5-mysql php5-sqlite php-apc > /dev/null 2>&1
# turn on PHP errors
sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/apache2/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/apache2/php.ini


# install Composer
echo -e "\n--- Installing Composer for PHP package management ---\n"
curl --silent https://getcomposer.org/installer | php > /dev/null 2>&1
mv composer.phar /usr/local/bin/composer


# install MySQL
echo -e "\n--- Install MySQL ---\n"
echo "mysql-server mysql-server/root_password password $DBPASSWD" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password $DBPASSWD" | debconf-set-selections
echo "phpmyadmin phpmyadmin/dbconfig-install boolean true" | debconf-set-selections
echo "phpmyadmin phpmyadmin/app-password-confirm password $DBPASSWD" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/admin-pass password $DBPASSWD" | debconf-set-selections
echo "phpmyadmin phpmyadmin/mysql/app-pass password $DBPASSWD" | debconf-set-selections
echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect none" | debconf-set-selections
apt-get -y install mysql-server-5.5 phpmyadmin > /dev/null 2>&1

echo -e "\n--- Configure MySQL ---\n"
mysql -uroot -p$DBPASSWD -e "CREATE DATABASE $DBNAME"
mysql -uroot -p$DBPASSWD -e "grant all privileges on $DBNAME.* to '$DBUSER'@'localhost' identified by '$DBPASSWD'"


# Configure Apache to use phpmyadmin
echo -e "\n--- Configure Apache to use phpmyadmin ---\n"
echo -e "\n\nListen 81\n" >> /etc/apache2/ports.conf

sudo mkdir /etc/apache2/conf-available
sudo touch /etc/apache2/conf-available/phpmyadmin.conf
sudo cat > /etc/apache2/conf-available/phpmyadmin.conf << "EOF"
<VirtualHost *:81>
    ServerAdmin webmaster@localhost
    DocumentRoot /usr/share/phpmyadmin
    DirectoryIndex index.php
    ErrorLog ${APACHE_LOG_DIR}/phpmyadmin-error.log
    CustomLog ${APACHE_LOG_DIR}/phpmyadmin-access.log combineddir
</VirtualHost>
EOF
a2enconf phpmyadmin > /dev/null 2>&1

echo -e "\n--- Add environment variables to Apache ---\n"
cat > /etc/apache2/sites-enabled/000-default.conf <<EOF
<VirtualHost *:80>
    DocumentRoot /var/www
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
    SetEnv APP_ENV $APPENV
    SetEnv DB_HOST $DBHOST
    SetEnv DB_NAME $DBNAME
    SetEnv DB_USER $DBUSER
    SetEnv DB_PASS $DBPASSWD
</VirtualHost>
EOF


# # install node.js incl npm
# echo -e "\n--- Install NodeJS ---\n"
# sudo curl https://raw.githubusercontent.com/creationix/nvm/v0.16.1/install.sh | sh

# #curl -sL https://deb.nodesource.com/setup | sudo bash - > /dev/null 2>&1
# #sudo apt-get -y install nodejs > /dev/null 2>&1
# #sudo npm -g install npm@latest > /dev/null 2>&1
# # curl --silent https://npmjs.org/install.sh | sh > /dev/null 2>&1


# Post processing
echo -e "\n--- Restarting Apache ---\n"
service apache2 restart > /dev/null 2>&1

# # update project
# echo -e "\n--- Updating project components and pulling latest versions ---\n"
# npm install -g gulp bower > /dev/null 2>&1
cd /vagrant
sudo composer install > /dev/null 2>&1

# # cd /vagrant/
# # sudo -u vagrant -H sh -c "yarn" #> /dev/null 2>&1
# # sudo -u vagrant -H sh -c "bower install -s" #> /dev/null 2>&1
# # sudo -u vagrant -H sh -c "gulp" #> /dev/null 2>&1

echo -e "\n--- Creating a symlink for future phpunit use ---\n"
ln -fs /vagrant/vendor/bin/phpunit /usr/local/bin/phpunit

echo -e "\n--- Add environment variables locally for artisan ---\n"
cat >> /home/vagrant/.bashrc <<EOF
# Set envvars
export APP_ENV=$APPENV
export DB_HOST=$DBHOST
export DB_NAME=$DBNAME
export DB_USER=$DBUSER
export DB_PASS=$DBPASSWD
EOF


# lastly, update database for mlocate in the background
sudo updatedb &
