# Installing Dependency
sudo apt-get install -y gcc make libzzip-dev libreadline-dev libxml2-dev libssl-dev libmcrypt-dev libcurl4-openssl-dev autoconf

# Go to current dir
cd sources/current

# Configure
./configure --prefix=/usr --with-config-file-path=/etc --enable-maintainer-zts

# Compile
sudo make && sudo make install

# Copy config
sudo cp php.ini-development /etc/php.ini

# Install pthread
sudo pecl install pthreads
echo "extension=pthreads.so" | sudo tee -a /etc/php.ini
