#!/bin/bash

# Corrige permissões
chown -R mysql:mysql /var/lib/mysql

if [ ! -d "/var/lib/mysql" ]; then
  echo "Inicializando banco de dados no volume..."
  mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql
fi

# Inicia o MySQL
service mysql start

# Aguarda o MySQL estar pronto
until mysqladmin ping --silent; do
  echo "Esperando MySQL iniciar..."
  sleep 1
done

# Cria o banco de dados
mysql -u root -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};"

# Cria o usuário e permissões
mysql -u root -e "CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'${DB_HOST}' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -u root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USERNAME}'@'${DB_HOST}';"
mysql -u root -e "FLUSH PRIVILEGES;"

# Importa o SQL, se existir
if [ -f /var/www/html/database.sql ]; then
  echo "Importando database.sql..."
  mysql -u root ${DB_NAME} < /var/www/html/database.sql
else
  echo "Arquivo database.sql não encontrado."
fi

# Escreve o arquivo .env
cat <<EOF > /var/www/html/.env
DB_HOST=${DB_HOST}
DB_NAME=${DB_NAME}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
EOF

# Inicia o Apache
service apache2 start

# Log
tail -f /var/log/apache2/access.log
