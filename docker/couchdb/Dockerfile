FROM couchdb:3.2.0

RUN echo 'root:root' | chpasswd # Mot de passe root : root

ARG GROUP_ID
ARG USER_ID
ARG USER_NAME

RUN groupadd -f -g $GROUP_ID $USER_NAME && \
    groupadd fpm && \
    useradd -u $USER_ID -g $GROUP_ID --create-home $USER_NAME && \
    usermod -a -G couchdb $USER_NAME && \
    usermod -a -G fpm $USER_NAME && \
    usermod -a -G www-data $USER_NAME && \
    chown "$USER_ID:$GROUP_ID" /opt/couchdb

USER $USER_NAME
