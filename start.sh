#!/bin/bash

# Gera o arquivo .env com variáveis de banco
echo "Gerando arquivo .env..."
cat <<EOF > /var/www/html/.env
DB_HOST=${DB_HOST}
DB_NAME=${DB_NAME}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
EOF

# Garante que diretório de certificado exista
mkdir -p /etc/apache2/ssl

# Gera certificado autoassinado se não existir
if [ ! -f /etc/apache2/ssl/cert.pem ] || [ ! -f /etc/apache2/ssl/privkey.pem ]; then
  echo "Gerando certificado SSL autoassinado..."
  openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/privkey.pem \
    -out /etc/apache2/ssl/cert.pem \
    -subj "/C=BR/ST=Estado/L=Cidade/O=Organizacao/OU=Unidade/CN=localhost"
fi

# Habilita o módulo SSL e default-ssl site
echo "Habilitando SSL no Apache..."
a2enmod ssl
a2ensite default-ssl

# Substitui caminho dos certificados no default-ssl.conf, se necessário
SSL_CONF="/etc/apache2/sites-available/default-ssl.conf"
sed -i "s|SSLCertificateFile.*|SSLCertificateFile /etc/apache2/ssl/cert.pem|" "$SSL_CONF"
sed -i "s|SSLCertificateKeyFile.*|SSLCertificateKeyFile /etc/apache2/ssl/privkey.pem|" "$SSL_CONF"

# Inicia o Apache
echo "Iniciando Apache..."
service apache2 start

# Mantém o container rodando com logs do Apache
echo "Container iniciado. Acompanhando logs do Apache..."
tail -f /var/log/apache2/access.log
