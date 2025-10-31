#!/bin/bash

# Script para corrigir permissões do Laravel no Docker
echo "🔐 Corrigindo permissões do Laravel..."

# Criar diretórios se não existirem
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Corrigir propriedade
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || {
    echo "⚠️  Não foi possível alterar o proprietário - executando como root"
    # Se não conseguir mudar o proprietário, pelo menos corrige as permissões
}

# Corrigir permissões
chmod -R 775 storage bootstrap/cache 2>/dev/null || {
    echo "⚠️  Não foi possível alterar permissões de alguns arquivos"
}

echo "✅ Permissões configuradas!"
