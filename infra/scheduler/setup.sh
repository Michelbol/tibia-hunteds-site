# install php
sudo apt update
sudo apt upgrade -y
sudo apt install -y software-properties-common ca-certificates lsb-release apt-transport-https
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.4 php8.4-cli php8.4-common php8.4-fpm php8.4-mysql php8.4-zip php8.4-gd php8.4-mbstring php8.4-curl php8.4-xml php8.4-bcmath
sudo apt install nano
php -v

# install composer
sudo apt update
sudo apt install php-cli unzip curl -y
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# install node
sudo apt install nodejs npm -y
curl -fsSL https://deb.nodesource.com/setup_XX.x | sudo -E bash -
sudo apt install nodejs -y

#install google chrome
sudo apt install wget -y
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo dpkg -i google-chrome-stable_current_amd64.deb
sudo apt --fix-broken install -y
sudo apt update
sudo apt install -y libgbm1 libnss3 libasound2t64 libatk-bridge2.0-0 libcups2 libdrm2 libxkbcommon0 libxcomposite1 libxdamage1 libxfixes3 libxrandr2 libxtst6 libpango-1.0-0 libcairo2 libappindicator3-1 libdbus-glib-1-2
sudo apt install -y libgconf-2-4 libgtk-3-0 libxss1 libexpat1 libfontconfig1 libfreetype6 libglib2.0-0 libjpeg-turbo8 libpng16-16 libstdc++6 libx11-6 libxext6 libxrender1 libxshmfence1 xdg-utils

#install mysql server
sudo apt install mysql-server
sudo mysql
CREATE USER 'tibia_scraper'@'localhost' IDENTIFIED BY 'admin';
CREATE USER 'tibia_scraper'@'%' IDENTIFIED BY 'admin';
GRANT ALL PRIVILEGES ON tibia_scraper.* TO 'tibia_scraper'@'localhost';
GRANT ALL PRIVILEGES ON *.* TO 'tibia_scraper'@'%';
FLUSH PRIVILEGES;
create database tibia_scraper;
exit;

#criar serviço no linux
sudo nano /etc/systemd/system/world-scraper.service
sudo touch /var/log/world-scraper.log
sudo chown www-data:www-data /var/log/world-scraper.log
sudo systemctl daemon-reload
sudo systemctl enable world-scraper.service
sudo systemctl start world-scraper.service
sudo systemctl status world-scraper.service
sudo tail -f /var/log/world-scraper.log