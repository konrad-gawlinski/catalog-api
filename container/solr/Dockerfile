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
    && tar -xf "/tmp/${SOLR_FILENAME}" -C "/usr/local/solr7" \
    && mkdir /var/solr_home && mkdir /var/solr_data

RUN adduser --disabled-password --gecos "" solr \
    && chown -R solr /usr/local/solr7 \
    && chown -R solr /var/solr_home && chown -R solr /var/solr_data

COPY conf/solr/solr.xml /var/solr_home/solr.xml
COPY conf/supervisor/ /etc/supervisor/conf.d

#RUN cd /var/solr_home \
#  && su solr -c "/usr/local/solr7/solr-7.2.1/bin/solr create_core -c product_catalog" \
#  && mv product_catalog/conf/managed-schema product_catalog/conf/schema.xml \
#  && sed -i '/<config>/a \  <schemaFactory class="ClassicIndexSchemaFactory"\/>\n' product_catalog/conf/solrconfig.xml
#
#remove schemaless update chain statements

#cleanup
RUN rm -rf /tmp/* \
    && apt-get purge -y --auto-remove gcc make wget

CMD ["supervisord", "-n"]
