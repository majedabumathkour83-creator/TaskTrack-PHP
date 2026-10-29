FROM php:8.4-apache

WORKDIR /var/www/html

COPY public/ ./
COPY src/ /var/www/src/

RUN mkdir -p /var/www/storage \
    && chown www-data:www-data /var/www/storage

EXPOSE 80

CMD ["apache2-foreground"]
