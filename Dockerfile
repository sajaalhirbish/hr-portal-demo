# Dockerfile : PHP + Apache + Oracle support for the HR portal
FROM php:8.3-apache-bookworm

# libaio1 is needed by Oracle's client library; unzip and wget download and unpack it
RUN apt-get update \
    && apt-get install -y --no-install-recommends libaio1 unzip wget ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Oracle Instant Client: the library PHP uses to talk to Oracle
RUN mkdir -p /opt/oracle && cd /opt/oracle \
    && wget -nv https://download.oracle.com/otn_software/linux/instantclient/instantclient-basiclite-linuxx64.zip \
    && wget -nv https://download.oracle.com/otn_software/linux/instantclient/instantclient-sdk-linuxx64.zip \
    && unzip -qo instantclient-basiclite-linuxx64.zip \
    && unzip -qo instantclient-sdk-linuxx64.zip \
    && rm -f *.zip \
    && mv instantclient_* instantclient \
    && echo /opt/oracle/instantclient > /etc/ld.so.conf.d/oracle-instantclient.conf \
    && ldconfig

# The PHP extension that lets PDO talk to Oracle
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient \
    && docker-php-ext-install pdo_oci

# Put the application code inside the image so it can run by itself on any server.
# (On your laptop, "docker run -v" replaces this folder, so your edits still show live.)
COPY . /var/www/html/