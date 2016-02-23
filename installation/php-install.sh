# Remove PHP
sudo apt-get remove php5 php-common -y

# Installing Dependency
sudo apt-get install -y gcc build-essential pkg-config make automake libzzip-dev libreadline-dev libxml2-dev libssl-dev libmcrypt-dev libcurl4-openssl-dev autoconf mysql-client imagemagick t1-dev libfontconfig1-dev libfreetype6-dev libgd-dev libgd2-xpm-dev libice-dev libjbig-dev libjpeg-dev libjpeg-turbo8-dev libjpeg8-dev liblzma-dev libpng12-dev libpthread-stubs0-dev libsm-dev libtiff5-dev libtiffxx5 libvpx-dev libx11-dev libx11-doc libxau-dev libxcb1-dev libxdmcp-dev libxpm-dev libmagickwand-dev openssh-server zip bzip2

# Go to current dir
cd sources/current

# Configure
./configure --prefix=/usr --with-curl --with-readline --enable-debug --with-config-file-path=/etc --enable-maintainer-zts --with-curl --with-mysqli=mysqlnd --with-openssl --enable-ftp --enable-bcmath --with-bz2 --enable-calendar --enable-exif --with-gd --with-gettext --enable-intl --enable-mbstring --enable-shmop --with-mhash --enable-pcntl --with-pdo-mysql --with-snmp --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvshm --enable-sysvsem --enable-wddx --enable-zip --with-zlib --enable-opcache

# Compile
sudo make clean && sudo make && sudo make install

# Copy config
sudo cp php.ini-development /etc/php.ini

# Install pthread
sudo pecl install pthreads memcache imagick-beta
echo "extension=pthreads.so" | sudo tee -a /etc/php.ini
echo "extension=imagick.so" | sudo tee -a /etc/php.ini
echo "extension=memcache.so" | sudo tee -a /etc/php.ini
