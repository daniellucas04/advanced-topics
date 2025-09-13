FROM php:8.2-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y git unzip curl

RUN docker-php-ext-install mysqli pdo pdo_mysql \
     && docker-php-ext-enable pdo_mysql

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Clona o projeto direto pra pasta HTML
RUN rm -rf /var/www/html/* && \
    git clone https://github.com/daniellucas04/advanced-topics.git /var/www/html && \
    rm -rf /var/www/html/.git

RUN composer install --no-dev --no-interaction

# Instala dependências do PHP
RUN composer install --no-interaction --no-dev --working-dir=/var/www/html

EXPOSE 80