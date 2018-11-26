#!/usr/bin/env bash

# Run only on Vagrant VM
grep -si vagrant /etc/network/interfaces >/dev/null
if [ $? -eq 1 ] ; then
    echo "This script should be run only under a Vagrant VM"
    exit 1
fi

WEBROOT="/var/www/html"
BUILD_SCRIPTS="$WEBROOT/build_scripts"

# Create a symlink to /vagrant
mkdir -p /var/www && chown -R vagrant /var/www
if [[ -d $WEBROOT && ! -L $WEBROOT ]] ; then
    rmdir $WEBROOT
elif [ -f $WEBROOT ] ; then
    rm $WEBROOT
fi

if [ ! -e $WEBROOT ] ; then
    ln -s /vagrant $WEBROOT
fi

# Check if locales are configured
grep -q GB /etc/locale.gen
if [ $? -eq 1 ] ; then
    echo "[INFO] Configuring locales..."
    echo -e "en_US.UTF-8 UTF-8\nen_GB ISO-8859-1\nen_GB.UTF-8 UTF-8\nen_US ISO-8859-1" > /etc/locale.gen
    /usr/sbin/locale-gen
fi

# Check if Dotdeb is loaded
grep -q dotdeb /etc/apt/sources.list
if [ $? -eq 1 ] ; then
    echo "[INFO] Adding Dotdeb repository..."
    echo -e "\ndeb http://packages.dotdeb.org wheezy all\ndeb-src http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list
    wget http://www.dotdeb.org/dotdeb.gpg && sudo apt-key add dotdeb.gpg && rm dotdeb.gpg

    # Force apt-get update
    if [ -f $BUILD_SCRIPTS/cache/last_apt_udpdate ] ; then
        rm $BUILD_SCRIPTS/cache/last_apt_udpdate
    fi
fi

#Add php5.6 repo
echo "deb http://packages.dotdeb.org wheezy-php56 all" >> /etc/apt/sources.list.d/dotdeb.list
echo "deb-src http://packages.dotdeb.org wheezy-php56 all" >> /etc/apt/sources.list.d/dotdeb.list

wget http://www.dotdeb.org/dotdeb.gpg -O- |apt-key add –

apt-get update

# Add esportsconstruct.dev to hosts file
grep -q esportsconstruct /etc/hosts
if [ $? -eq 1 ] ; then
    echo "[INFO] Adding esportsconstruct.dev to hosts file..."
    echo -e "\n10.99.0.99\tesportsconstruct.dev\n10.99.0.99\twww.esportsconstruct.dev" >> /etc/hosts
fi

# First time update
if [ ! -f $BUILD_SCRIPTS/cache/last_apt_udpdate ] ; then
    touch -d '-1 month' $BUILD_SCRIPTS/cache/last_apt_udpdate
fi

# Update the packages once a day
touch -d '-1 day' $BUILD_SCRIPTS/cache/yesterdays_timestamp
if [ $BUILD_SCRIPTS/cache/yesterdays_timestamp -nt $BUILD_SCRIPTS/cache/last_apt_udpdate ]; then
    apt-get update
    touch $BUILD_SCRIPTS/cache/last_apt_udpdate
fi

# Configure MySQL password
debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

# Install packages if they aren't installed yet
for PACKAGE in {mysql-server,vim,nano,htop,nmon,apache2,php5,php5-cli,php5-apcu,php-pear,php5-curl,php5-mcrypt,php5-mysqlnd,php5-imagick,php5-gd,php5-redis,php-pear,php5-fpm,php5-dev,php5-xdebug,git,inotify-tools}; do
    dpkg -s $PACKAGE &> /dev/null
    if [ $? -eq 1 ] ; then
        echo "[INFO] Installing package $PACKAGE..."
        apt-get install -y --force-yes $PACKAGE
    fi
done

# Remove unnecessary packages if present
for PACKAGE in {iptables,nginx}; do
    echo "[INFO] Removing package $PACKAGE..."
    apt-get remove -y --force-yes $PACKAGE
done

echo "[INFO] Bootstrapping environment..."
cd $BUILD_SCRIPTS/bootstrap
for SHARED_RESOURCE in `find -type f -exec echo {} \; | sed -e 's/^..//' ` ; do
    cp -f $BUILD_SCRIPTS/bootstrap/$SHARED_RESOURCE /$SHARED_RESOURCE 2>/dev/null
    if [ $? -eq 0 ] ; then
        echo "  /$SHARED_RESOURCE"
    fi
done

if [ -f /etc/apache2/sites-enabled/000-default.conf ] ; then
    rm /etc/apache2/sites-enabled/000-default.conf
fi

chsh -s /bin/bash vagrant

# Install OpCache
pecl install zendopcache
if [ ! -f /usr/lib/php5/20100525/opcache.so ] ; then
    OPCACHE=`find /usr/lib | grep opcache.so | head -n 1`
    mkdir -p /usr/lib/php5/20100525
    ln -s $OPCACHE /usr/lib/php5/20100525/opcache.so
fi

echo "[INFO] Enabling mod_php and mod_rewrite..."
# Enable Apache mod PHP and Mod Rewrite
cd /etc/apache2/mods-enabled

sudo ln -s ../mods-available/php5.conf php5.conf
sudo ln -s ../mods-available/php5.load php5.load

sudo a2enmod rewrite


echo "[INFO] Reloading services..."
for SERVICE in {apache2,mysql} ; do
    service $SERVICE restart
done

# Install composer globally
if [ ! -e /usr/local/bin/composer ] ; then
    cd /usr/local/bin
    curl -sS https://getcomposer.org/installer | /usr/bin/php
    mv composer.phar composer && chmod +x composer && chown vagrant composer
fi

# Install nodejs and npm globally
if [ ! -e /usr/local/bin/node ] ; then
    curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -
    apt-get install -y nodejs
fi

# Install bower globally
npm install bower -g

# Install Redis
cd ~
wget http://download.redis.io/releases/redis-stable.tar.gz
tar xzf redis-stable.tar.gz
cd redis-stable
make
make test
make install
cd utils
sudo ./install_server.sh
service redis_6379 start

if [ ! -f $BUILD_SCRIPTS/cache/initialized ]; then
    echo "[INFO] Performing initial setup..."
    cd $BUILD_SCRIPTS

    ./init.sh
fi

echo "[INFO] Post-bootstrapping environment..."
cd $BUILD_SCRIPTS/bootstrap
for SHARED_RESOURCE in `find -type f -exec echo {} \; | sed -e 's/^..//' ` ; do
    if [ ! -f /$SHARED_RESOURCE ] ; then
        sudo -u vagrant cp -f $BUILD_SCRIPTS/bootstrap/$SHARED_RESOURCE /$SHARED_RESOURCE
        echo "  /$SHARED_RESOURCE"
    fi
done
