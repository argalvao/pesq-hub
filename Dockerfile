# Use PHP 8.3 com Apache
FROM php:8.3-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    sqlite3 \
    libsqlite3-dev \
    postgresql-client \
    && docker-php-ext-install pdo_mysql pdo_sqlite pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache
RUN a2enmod rewrite
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar apenas arquivos de dependências primeiro (para cache de camada)
COPY composer.json composer.lock ./

# Copiar .env.example para .env (necessário para scripts do composer)
COPY .env.example .env

# Instalar dependências do PHP (sem executar scripts que precisam do Laravel configurado)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar resto dos arquivos da aplicação
COPY . .

# Criar estrutura completa de diretórios
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# Configurar permissões para o Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copiar script de inicialização
COPY docker/init-app.sh /usr/local/bin/init-app.sh
RUN chmod +x /usr/local/bin/init-app.sh

# Expor porta
EXPOSE 80

# Comando de inicialização
CMD ["/usr/local/bin/init-app.sh"]
