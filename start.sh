#!/bin/bash

# Inicia o MySQL
service mysql start

# Aguarda o MySQL subir (ajuste se necessário)
echo "Aguardando MySQL subir..."
sleep 5

# Cria o banco de dados (se não existir)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS topics;"

# Importa o arquivo SQL
if [ -f /var/www/html/database.sql ]; then
    echo "Importando database.sql..."
    mysql -u root topics < /var/www/html/database.sql
else
    echo "Arquivo database.sql não encontrado."
fi

echo "DB_HOST=${DB_HOST}" > /var/www/html/.env
echo "DB_NAME=${DB_NAME}" >> /var/www/html/.env
echo "DB_USERNAME=${DB_USERNAME}" >> /var/www/html/.env
echo "DB_PASSWORD=${DB_PASSWORD}" >> /var/www/html/.env

# Inicia o Apache
service apache2 start

tail -f /var/log/apache2/access.log