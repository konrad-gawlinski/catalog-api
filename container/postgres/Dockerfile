FROM debian:jessie

RUN apt-get update && apt-get install -y gcc make wget
RUN apt-get install -y supervisor libreadline-dev zlib1g-dev less git curl lbzip2 g++

#install postgresql server-client
ENV PGSQL_VERSION 9.6.3
ENV PGSQL_FILENAME "postgresql-${PGSQL_VERSION}.tar.gz"

RUN wget "https://ftp.postgresql.org/pub/source/v${PGSQL_VERSION}/${PGSQL_FILENAME}" -O "/tmp/${PGSQL_FILENAME}" \
    && tar -xf "/tmp/${PGSQL_FILENAME}" -C "/tmp/" \
    && cd "/tmp/postgresql-${PGSQL_VERSION}" && "./configure" \
        --prefix=/usr/local/pgsql \
    && make && make install

#initialize postgresql
RUN echo '' >> /etc/profile \
    && echo 'export PGDATA=/var/pgsql_data' >> /etc/profile \
    && adduser --disabled-password --gecos "" postgres \
    && mkdir -p /var/pgsql_data && chown postgres:postgres /var/pgsql_data && chmod 770 /var/pgsql_data \
    && mkdir -p /var/log/postgres && chown postgres:postgres /var/log/postgres && chmod 774 /var/log/postgres \
    && su -m postgres -c "/usr/local/pgsql/bin/initdb -D /var/pgsql_data" \
#    && su -m postgres -c "ALTER ROLE postgres WITH PASSWORD 'postgres'" \ this should be run after pgsql server is runing
    && printf "\nhost\tall\t\tpostgres\tsamenet\t\t\tmd5\n" >> /var/pgsql_data/pg_hba.conf

#install pl/v8
ENV PLV8_VERSION 2.0.0
ENV PLV8_FILENAME "plv8-v${PLV8_VERSION}.tar.gz"
RUN wget "https://github.com/plv8/plv8/archive/v${PLV8_VERSION}.tar.gz" -O "/tmp/${PLV8_FILENAME}" \
    && tar -xf "/tmp/${PLV8_FILENAME}" -C "/tmp/" \
    && cd "/tmp/plv8-${PLV8_VERSION}" \
    && make static PG_CONFIG=/usr/local/pgsql/bin/pg_config \
    && make install PG_CONFIG=/usr/local/pgsql/bin/pg_config

COPY conf/supervisor/ /etc/supervisor/conf.d

#cleanup
RUN rm -rf /tmp/* \
    && apt-get purge -y --auto-remove gcc g++ make wget git curl lbzip2

CMD ["supervisord", "-n"]
