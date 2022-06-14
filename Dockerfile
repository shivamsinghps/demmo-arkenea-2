FROM php:7.1-apache

WORKDIR "/var/www/html"

ENV APACHE_DOCUMENT_ROOT /var/www/html/web

COPY . /src
COPY crontab /etc/cron.d/fmt-cron
COPY upload_files.ini /usr/local/etc/php/conf.d/upload_files.ini

RUN rm -rf /var/www/html && mv /src /var/www/html && cd /var/www/html

RUN chmod 0644 /etc/cron.d/fmt-cron

RUN sed -i -e "s+/var/www/html+$APACHE_DOCUMENT_ROOT+g" /etc/apache2/sites-available/*.conf && \
    sed -i -e "s+/var/www/+${APACHE_DOCUMENT_ROOT}/+g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt-get update && \
    apt-get install -y git curl libmcrypt-dev libreadline-dev libzip-dev zlib1g-dev libmcrypt-dev cron libxml2-dev

RUN docker-php-ext-configure zip --with-libzip && \
    docker-php-ext-install mysqli pdo pdo_mysql zip soap && \
    docker-php-ext-enable soap
RUN echo 'max_execution_time = -1 ' >> /usr/local/etc/php/conf.d/upload_files.ini

### Installl Node
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash && \
    apt-get install -y nodejs sendmail && \
    apt clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    npm install gulp bower -g && \
    npm install less less-loader -g


RUN crontab /etc/cron.d/fmt-cron

RUN php phing.phar -verbose

RUN chmod 777 -R /var\
    && chmod 777 -R /var/cache

RUN a2enmod headers\
    && a2enmod rewrite

CMD phing buildphp phing.phar deploy -verbose ; apache2-foreground

