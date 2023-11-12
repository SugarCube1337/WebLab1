FROM php:apache

COPY . /var/www/html

EXPOSE 8080

RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]