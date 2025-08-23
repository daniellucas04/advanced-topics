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

# Inicia o Apache
service apache2 start

tail -f /var/log/apache2/access.log