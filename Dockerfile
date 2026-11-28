FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql

RUN a2enmod rewrite

WORKDIR /var/www/html/galezarzal

COPY . .

RUN mkdir -p logs && chown -R www-data:www-data logs

RUN printf '%s\n' \
    '<DirectoryMatch "^/var/www/html/galezarzal/(database|logs|docs|features|scripts)(/|$)">' \
    '    Require all denied' \
    '</DirectoryMatch>' \
    '<FilesMatch "^(\.|Dockerfile$|docker-compose\.yml$|README\.md$)">' \
    '    Require all denied' \
    '</FilesMatch>' \
    > /etc/apache2/conf-available/galezarzal-security.conf \
    && a2enconf galezarzal-security

EXPOSE 80
