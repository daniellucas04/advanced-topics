# Usando uma imagem base do Ubuntu
FROM ubuntu

# Evitando perguntas interativas durante a instalação de pacotes
ENV DEBIAN_FRONTEND=noninteractive

# Atualizando os pacotes e instalando Apache, MySQL, PHP 8.2, Git e extensões
RUN apt-get update
RUN apt-get install -y apache2
# Definindo ServerName para evitar warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN apt-get install -y mysql-server
RUN apt-get install -y git
# Adicionando o repositório de PHP 8.2
RUN apt-get install -y software-properties-common
RUN add-apt-repository ppa\:ondrej/php -y
RUN apt-get update
# Instalando PHP 8.2 e a extensão PDO MySQL
RUN apt-get install -y php8.2
RUN apt-get install -y php8.2-mysql
RUN apt-get install -y php8.2-mbstring

# Habilitando o módulo PHP no Apache
RUN a2enmod php8.2

# Clonando o projeto e instalando dependências PHP com Composer
RUN apt-get update && \
    apt-get install -y unzip git curl php-cli && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Clonando o repositório do GitHub para a pasta do Apache
RUN git clone https://github.com/daniellucas04/advanced-topics.git
RUN rm -rf /var/www/html/*
RUN cp -r advanced-topics/* /var/www/html/
RUN rm -rf advanced-topics
RUN cd /var/www/html/
RUN composer install --no-interaction --no-dev --working-dir=/var/www/html

# Definindo permissões para o Apache acessar os arquivos clonados
RUN chown -R www-data:www-data /var/www/html
RUN echo 'variables_order = "EGPCS"' >> /etc/php/8.2/apache2/php.ini

RUN chmod +x /var/www/html/start.sh
CMD ["/var/www/html/start.sh"]

VOLUME /var/lib/mysql

# (Apache) e (MySQL)
EXPOSE 80 3306