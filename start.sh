#!/bin/bash

# Corrige permissões
chown -R mysql:mysql /var/lib/mysql

if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Inicializando banco de dados MariaDB..."
    mysql_install_db --user=mysql --datadir=/var/lib/mysql > /dev/null
    service mariadb start
fi

# Aguarda o MariaDB estar pronto
until mysqladmin ping --silent; do
  echo "Esperando MariaDB iniciar..."
  sleep 1
done

# Cria o banco de dados se não existir
echo "Criando banco de dados '${DB_NAME}'..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`;"

# Cria o usuário e define permissões
echo "Criando usuário '${DB_USERNAME}' com acesso a '${DB_NAME}'..."
mysql -u root -e "CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'${DB_HOST}' IDENTIFIED BY '${DB_PASSWORD}';"
mysql -u root -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USERNAME}'@'${DB_HOST}';"
mysql -u root -e "FLUSH PRIVILEGES;"

# Importa o SQL se o arquivo existir
if [ -f /var/www/html/database.sql ]; then
  echo "Importando database.sql..."
  mysql -u root ${DB_NAME} < /var/www/html/database.sql
else
  echo "Arquivo database.sql não encontrado. Ignorando importação."
fi

# Gera o arquivo .env com variáveis de banco
echo "Gerando arquivo .env..."
cat <<EOF > /var/www/html/.env
DB_HOST=${DB_HOST}
DB_NAME=${DB_NAME}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
EOF

# Inicia o Apache
echo "Iniciando Apache..."
service apache2 start

# Mantém o container rodando com logs do Apache
echo "Container iniciado. Acompanhando logs do Apache..."
tail -f /var/log/apache2/access.log
