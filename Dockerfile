FROM php:8.3-apache

RUN a2enmod rewrite \
  && docker-php-ext-install pdo pdo_mysql \
  && apt-get update \
  && apt-get install -y --no-install-recommends ca-certificates curl \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/app

# Point Apache at /public
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/app/public#g' /etc/apache2/sites-available/000-default.conf \
  && sed -i 's#<Directory /var/www/>#<Directory /var/www/app/public/>#g' /etc/apache2/apache2.conf \
  && printf "\n<Directory /var/www/app/public/>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf
