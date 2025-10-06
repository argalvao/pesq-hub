#!/bin/bash

# Aguardar alguns segundos para garantir que tudo esteja pronto
sleep 5

# Configurar permissões corretas ANTES de executar comandos Laravel
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Criar diretórios se não existirem
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/sessions  
mkdir -p /var/www/html/storage/framework/cache
chown -R www-data:www-data /var/www/html/storage/framework
chmod -R 775 /var/www/html/storage/framework

# Executar comandos Laravel (sem migrações pois usamos Google Sheets)
php artisan key:generate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Inicializar dados do Google Sheets (se necessário)
php artisan sheets:init || echo "Comando sheets:init não executado ou falhou"

# Iniciar Apache
apache2-foreground
