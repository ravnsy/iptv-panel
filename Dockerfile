FROM php:8.2-apache

# حل مشكلة تعارض وحدات MPM التي تسبب الانهيار
RUN a2dismod mpm_event || true
RUN a2enmod mpm_prefork || true

# نسخ ملفاتك
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
