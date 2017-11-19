FROM debian:jessie

RUN echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu xenial main" | tee /etc/apt/sources.list.d/webupd8team-java.list \
    && echo "deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu xenial main" | tee -a /etc/apt/sources.list.d/webupd8team-java.list \
    && apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886

RUN apt-get update && apt-get install -y gcc make wget
RUN printf 'yes\n' | apt-get install -y supervisor oracle-java8-installer lsof

#install solr sever
ENV SOLR_VERSION 7.1.0
ENV SOLR_FILENAME "solr-${SOLR_VERSION}.tgz"

RUN wget "wget http://mirror.funkfreundelandshut.de/apache/lucene/solr/${SOLR_VERSION}/${SOLR_FILENAME}" -O "/tmp/${SOLR_FILENAME}" \
    && mkdir /usr/local/solr7 \
    && tar -xf "/tmp/${SOLR_FILENAME}" -C "/usr/local/solr7" \
    && mkdir -p "/usr/local/solr7/solr-7.1.0/example/testdrive/solr"

COPY conf/supervisor/ /etc/supervisor/conf.d

#cleanup
RUN rm -rf /tmp/* \
    && apt-get purge -y --auto-remove gcc make wget

CMD ["supervisord", "-n"]

#docker run -it -p 8983:8983 --network=catalog-api --name=solr-v7 private/solr-v7 /bin/bash
#"bin/solr" start -cloud -p 8983 -s "example/cloud/node1/solr" -force