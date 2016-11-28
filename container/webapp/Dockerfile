FROM debian:jessie

RUN apt-get update && apt-get install -y gcc make wget autoconf
RUN apt-get install -y supervisor libpcre3-dev zlib1g-dev libxml2-dev git

#install postgresql client
ENV PGSQL_VERSION 9.6.0
ENV PGSQL_FILENAME "postgresql-${PGSQL_VERSION}.tar.gz"

RUN wget "https://ftp.postgresql.org/pub/source/v${PGSQL_VERSION}/${PGSQL_FILENAME}" -O "/tmp/${PGSQL_FILENAME}" \
    && tar -xf "/tmp/${PGSQL_FILENAME}" -C "/tmp/" \
    && cd "/tmp/postgresql-${PGSQL_VERSION}" && "./configure" \
        --prefix=/usr/local/pgsql \
        --without-readline \
    && make -C src/bin install \
    && make -C src/backend install \
    && make -C src/include install \
    && make -C src/interfaces install

#install nginx
ENV NGINX_VERSION nginx-1.10.1
ENV NGINX_FILENAME "${NGINX_VERSION}.tar.gz"

RUN wget "http://nginx.org/download/${NGINX_FILENAME}" -O "/tmp/${NGINX_FILENAME}" \
    && tar -xf "/tmp/${NGINX_FILENAME}" -C "/tmp/" \
    && cd "/tmp/${NGINX_VERSION}" && "./configure" \
        --prefix=/usr/local/nginx \
        --error-log-path=/var/log/nginx/error.log \
        --http-log-path=/var/log/nginx/access.log \
        --user=www-data \
        --group=www-data \
        --without-http_proxy_module \
    && make && make install

COPY conf/http/ /usr/local/nginx/conf/

#install php7
ENV PHP_VERSION php-7.0.11
ENV PHP_FILENAME "${PHP_VERSION}.tar.gz"

RUN wget "http://php.net/get/${PHP_FILENAME}/from/this/mirror" -O "/tmp/${PHP_FILENAME}" \
    && tar -xf "/tmp/${PHP_FILENAME}" -C "/tmp/" \
    && cd "/tmp/${PHP_VERSION}" && "./configure" \
        --prefix=/usr/local/php-fpm \
        --disable-cgi \
        --with-pgsql=/usr/local/pgsql \
        --enable-fpm --with-fpm-user=www-data --with-fpm-group=www-data \
    && make && make install \
    && mkdir -p /usr/html \
    && mkdir /var/log/php \
    && cd /usr/bin && ln -s /usr/local/php-fpm/bin/php php \
    && wget "https://getcomposer.org/download/1.2.2/composer.phar" -O "/usr/local/composer.phar"

COPY conf/fpm/ /usr/local/php-fpm/etc/
COPY conf/php.ini /usr/local/php-fpm/lib/
COPY src/ /var/webapp/
COPY conf/supervisor/ /etc/supervisor/conf.d

#install xdebug
ENV XDEBUG_VERSION xdebug-2.4.1
ENV XDEBUG_FILENAME "${XDEBUG_VERSION}.tgz"

RUN wget "https://xdebug.org/files/${XDEBUG_FILENAME}" -O "/tmp/${XDEBUG_FILENAME}" \
    && tar -xf "/tmp/${XDEBUG_FILENAME}" -C "/tmp/" \
    && cd "/tmp/${XDEBUG_VERSION}" && /usr/local/php-fpm/bin/phpize \
    && "./configure" \
        --enable-xdebug \
        --with-php-config=/usr/local/php-fpm/bin/php-config \
    && make && make install \
    && sed -i 's/;insert_extension/\nzend_extension=xdebug.so;\nxdebug.remote_enable=1;insert_extension/g' /usr/local/php-fpm/lib/php.ini

RUN rm -rf /tmp/* \
    && apt-get purge -y --auto-remove gcc make wget autoconf

CMD ["supervisord", "-n"]
