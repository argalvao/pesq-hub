#!/bin/bash

echo "🚀 Iniciando aplicação PesqHub..."

# Verificar e instalar dependências do Composer se necessário
if [ ! -d "/var/www/html/vendor" ] || [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "📦 Instalando dependências do Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
    echo "✅ Dependências instaladas!"
else
    echo "✅ Dependências do Composer já instaladas!"
fi

# Aguardar o banco de dados estar pronto
echo "⏳ Aguardando banco de dados..."
while ! pg_isready -h db -p 5432 -U root; do
    sleep 1
done

echo "✅ Banco de dados conectado!"

# Executar migrações
echo "📋 Executando migrações..."
php artisan migrate --force

# Executar seeders (popular banco)
echo "🌱 Populando banco de dados..."
php artisan db:seed --force

# Corrigir permissões (importante para volumes montados)
echo "🔐 Corrigindo permissões..."
chown -R www-data:www-data /var/www/html/storage/framework /var/www/html/storage/logs /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage/framework /var/www/html/storage/logs /var/www/html/bootstrap/cache 2>/dev/null || true

# Limpar caches
echo "🧹 Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ Aplicação inicializada com sucesso!"

# Iniciar Apache
echo "🌐 Iniciando servidor web..."
exec apache2-foreground

