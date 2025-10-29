#!/bin/bash

echo "🚀 Iniciando aplicação PesqHub..."

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
