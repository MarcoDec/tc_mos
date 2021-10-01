FROM debian:11.0

ENV DEBIAN_FRONTEND noninteractive

RUN apt update && \
    apt upgrade -y --no-install-recommends && \
    apt autoremove -y && \
    apt install -y --no-install-recommends apt-utils debconf

RUN apt install -y --no-install-recommends apache2

RUN a2enmod proxy_fcgi ssl rewrite proxy proxy_balancer proxy_http proxy_ajp

RUN sed -i '/Global configuration/a \
ServerName localhost \
' /etc/apache2/apache2.conf

EXPOSE 80 443

RUN rm -f /run/apache2/apache2.pid

CMD apachectl  -DFOREGROUND -e info
