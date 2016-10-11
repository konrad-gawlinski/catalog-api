FROM debian:jessie

RUN apt-get update && apt-get install -y gcc make wget
RUN apt-get install -y supervisor libreadline-dev zlib1g-dev

#install postgresql server-client
ENV PGSQL_VERSION 9.6.0
ENV PGSQL_FILENAME "postgresql-${PGSQL_VERSION}.tar.gz"

RUN wget "https://ftp.postgresql.org/pub/source/v${PGSQL_VERSION}/${PGSQL_FILENAME}" -O "/tmp/${PGSQL_FILENAME}" \
    && tar -xf "/tmp/${PGSQL_FILENAME}" -C "/tmp/" \
    && cd "/tmp/postgresql-${PGSQL_VERSION}" && "./configure" \
        --prefix=/usr/local/pgsql \
    && make && make install

#initialize postgresql
RUN echo '' >> /etc/profile \
    && echo 'export PGDATA=/var/pgsql_data' \
    && adduser --disabled-password --gecos "" postgres \
    && mkdir -p /var/pgsql_data && chown postgres:postgres /var/pgsql_data && chmod 770 /var/pgsql_data \
    && mkdir -p /var/log/postgres && chown postgres:postgres /var/log/postgres && chmod 774 /var/log/postgres \
    && su -m postgres -c "/usr/local/pgsql/bin/initdb -D /var/pgsql_data" \
    && printf "\nhost\tall\t\tpostgres\tsamenet\t\t\tmd5\n" >> /var/pgsql_data/pg_hba.conf

COPY conf/supervisor/ /etc/supervisor/conf.d

#cleanup
RUN rm -rf /tmp/* \
    && apt-get purge -y --auto-remove gcc make wget

CMD ["supervisord", "-n"]
