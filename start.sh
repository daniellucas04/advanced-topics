#!/bin/bash

# Gera o arquivo .env com variáveis de banco
echo "Gerando arquivo .env..."
cat <<EOF > /var/www/html/.env
DB_HOST=${DB_HOST}
DB_NAME=${DB_NAME}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
EOF