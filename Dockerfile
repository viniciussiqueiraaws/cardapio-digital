# Dockerfile do CardápioDigital
# Curso "Do Zero à Nuvem" · Aula 4.3
#
# Imagem: php:8.4-fpm-bookworm
# Stack: Nginx + PHP-FPM 8.4 (Debian 12)
# Servidor web: Nginx servindo conteúdo estático e passando .php para PHP-FPM
#
# Build:    docker build -t cardapio-digital:v1 .
# Run:      docker run -d -p 8080:80 \
#             -e DB_HOST=... -e DB_USER=... -e DB_PASS=... -e DB_NAME=... \
#             --name cardapio-container cardapio-digital:v1

FROM php:8.4-fpm-bookworm

# Instala Nginx (servidor web) e dependências do MySQL
RUN apt-get update && apt-get install -y \
      nginx \
      default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Instala a extensão mysqli do PHP (o index.php usa mysqli_connect)
RUN docker-php-ext-install pdo_mysql mysqli

# Copia configuração customizada do Nginx
# (logs para stdout/stderr para CloudWatch capturar)
COPY nginx.conf /etc/nginx/sites-available/default

# Copia configuração customizada do PHP-FPM pool
# (clear_env = no permite que env vars da Task Definition cheguem ao getenv())
COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Copia o código da aplicação
COPY index.php /var/www/html/index.php

# Permissões para www-data acessar os arquivos
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta 80 (Nginx)
EXPOSE 80

# Inicia PHP-FPM em background e Nginx em foreground
CMD php-fpm -D && nginx -g "daemon off;"