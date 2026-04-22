FROM php:8.2-apache

# حل مشكلة تعارض MPM
RUN a2dismod mpm_event || true
RUN a2enmod mpm_prefork || true

# تفعيل rewrite
RUN a2enmod rewrite

# نسخ الملفات
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# إنشاء مجلد لقاعدة البيانات مع صلاحيات كتابة
RUN mkdir -p /var/www/html/data && chown -R www-data:www-data /var/www/html/data

EXPOSE 80
