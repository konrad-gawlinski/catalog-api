FROM debian:jessie

RUN apt-get update && apt-get install -y gcc make wget
RUN apt-get install -y supervisor lsof

#install java
#copy java tar file to /container/solr/bin/
COPY bin/jdk-8u152-linux-x64.tar.gz /tmp/jdk-8u152-linux-x64.tar.gz

RUN tar -zxf "/tmp/jdk-8u152-linux-x64.tar.gz" -C /usr/local \
    && update-alternatives --install /usr/bin/java java /usr/local/jdk1.8.0_152/bin/java 100 \
    && update-alternatives --install /usr/bin/javac javac /usr/local/jdk1.8.0_152/bin/javac 100

#install solr sever
ENV SOLR_VERSION 7.2.1
ENV SOLR_FILENAME "solr-${SOLR_VERSION}.tgz"

RUN wget "http://mirror.funkfreundelandshut.de/apache/lucene/solr/${SOLR_VERSION}/${SOLR_FILENAME}" -O "/tmp/${SOLR_FILENAME}" \
    && mkdir /usr/local/solr7 \
    && tar -xf "/tmp/${SOLR_FILENAME}" -C "/usr/local/solr7"

COPY conf/supervisor/ /etc/supervisor/conf.d

#cleanup
RUN rm -rf /tmp/* \
    && apt-get purge -y --auto-remove gcc make wget

CMD ["supervisord", "-n"]

#docker run -it -p 8983:8983 --network=catalog-api --name=solr-v7 private/solr-v7 /bin/bash
#"bin/solr" start -cloud -p 8983 -s "example/cloud/node1/solr" -force