#####################################################################
# $> docker build -t gnmaag:[22] .
# $> docker run -d --restart=always -p 80:8080 serversideup
# :[22]

# Creamos entrada en el host file   127.0.0.1   gnmaag.reg.desa
# $> curl http://gnmaag.reg.desa
# $> curl https://gnmaag.reg.desa --insecure

# FROM serversideup/php:7.4.33-fpm-apache
# ENV APACHE_DOCUMENT_ROOT /var/www/html/public
# ENV SSL_MODE="off"
# EXPOSE 8080
# RUN mkdir -p /var/www/html/public
# Crear archivo index.php con el siguiente
#   <?php
#       phpinfo();
#   ?>
# COPY --chown=www-data:www-data ./index.php /var/www/html/public
#####################################################################


# https://hub.docker.com/layers/serversideup/php/7.4.33-fpm-apache/images/sha256-55276c45fef6e124e44931ac96e52c5094de162def5cbad45f485422cae949ce?context=explore
FROM serversideup/php:7.4-fpm-apache

# Switch to root so we can do root things
USER root
# Install the intl extension with root permissions
RUN install-php-extensions soap gd calendar
# Drop back to our unprivileged user
USER www-data

# Default port http
EXPOSE 8080

# Sets the directory from which Apache will serve files
# Defining Document Root in CakePhP
ENV APACHE_DOCUMENT_ROOT /var/www/html/app/webroot

# Configure how you would like to handle SSL. This can be "off" (HTTP only), "mixed" (HTTP + HTTPS), or "full" (HTTPS only). If you use HTTP, you may need to also change PHP_SESSION_COOKIE_SECURE
ENV SSL_MODE="off"

# PHP_SESSION_COOKIE_SECURE
# Default: 1 (true)
# Specifies whether cookies should only be sent over secure connections. (Official docs)	all
ENV PHP_SESSION_COOKIE_SECURE="1"

# Copy source files and config file
COPY --chown=www-data:www-data . /var/www/html/

# Create Directory if not Exists in DockerFile to cakephp 2
RUN mkdir -p /var/www/html/app/tmp
RUN mkdir -p /var/www/html/app/tmp/cache
RUN mkdir -p /var/www/html/app/tmp/logs
RUN mkdir -p /var/www/html/app/tmp/sessions
# Storage appfiles          => /app/files/
RUN mkdir -p /var/www/html/app/files
# /app/files/tmp            => Ver los logs de cakephp (originalmente en /app/tmp/logs)
RUN mkdir -p /var/www/html/app/files/tmp
RUN mkdir -p /var/www/html/app/webroot
# Storage appwebrootfiles   => /app/webroot/files
RUN mkdir -p /var/www/html/app/webroot/files

# Otorgamos permisos de escritura al usuario www-data en los directorios necesarios
RUN chmod -R 777 /var/www/html/app/tmp
RUN chmod -R 777 /var/www/html/app/tmp/cache
RUN chmod -R 777 /var/www/html/app/tmp/sessions
RUN chmod -R 777 /var/www/html/app/tmp/logs
RUN chmod -R 777 /var/www/html/app/files
RUN chmod -R 777 /var/www/html/app/files/tmp
RUN chmod -R 777 /var/www/html/app/webroot
RUN chmod -R 777 /var/www/html/app/webroot/files

# Custom PHP.ini settings
COPY ./zzz-custom-php.ini /usr/local/etc/php/conf.d/