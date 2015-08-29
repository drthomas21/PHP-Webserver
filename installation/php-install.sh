# Remove PHP
sudo apt-get remove php5 php-common -y

# Installing Dependency
sudo apt-get install -y gcc make automake libzzip-dev libreadline-dev libxml2-dev libssl-dev libmcrypt-dev libcurl4-openssl-dev autoconf

# Go to current dir
cd sources/current

# Configure
./configure --prefix=/usr --with-curl --with-config-file-path=/etc --enable-maintainer-zts

# Compile
sudo make clean && sudo make && sudo make install

# Copy config
sudo cp php.ini-development /etc/php.ini

# Install pthread
sudo pecl install pthreads
echo "extension=pthreads.so" | sudo tee -a /etc/php.ini
echo "extension=raphf.so" | sudo tee -a /etc/php.ini
echo "extension=propro.so" | sudo tee -a /etc/php.ini

#Other stuff
sudo apt-get install -y php5-raphf php5-propro
sudo pecl install raphf propro
