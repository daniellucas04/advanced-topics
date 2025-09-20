#!/bin/bash

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
