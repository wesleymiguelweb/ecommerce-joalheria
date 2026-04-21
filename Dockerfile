FROM php:8.4-apache

# Instalar extensões necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Ativar módulo Rewrite do Apache
RUN a2enmod rewrite

# Copiar os arquivos do projeto
COPY . /var/www/html

# Ajustar permissões para o Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Ajustar o Apache para apontar para a pasta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
# Adicione esta linha antes do final do seu Dockerfile
# ... (mantenha tudo até a linha 27 igual)

# Ajustar permissões para que o servidor consiga ler/escrever
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Gerar o mapeamento de classes (substitua a antiga 30 por esta)
RUN composer dump-autoload --optimize --no-scripts

EXPOSE 80

