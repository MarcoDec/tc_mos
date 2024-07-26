FROM redis:latest

ARG GROUP_ID
ARG USER_ID
ARG USER_NAME

RUN groupadd -f -g $GROUP_ID $USER_NAME && \
    useradd -u $USER_ID -g $GROUP_ID -d /var/www/html -s /bin/zsh $USER_NAME && \
    mkdir -p /var/www/html && \
    chown -R $USER_NAME:$USER_NAME /var/www/html

USER $USER_NAME

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 6379

CMD ["redis-server", "/etc/redis/redis.conf"]
