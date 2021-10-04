FROM mysql:8.0.26

RUN echo 'root:root' | chpasswd # Mot de passe root : root

ARG GROUP_ID
ARG USER_ID
ARG USER_NAME

RUN groupadd -f -g $GROUP_ID $USER_NAME && \
    groupadd fpm && \
    useradd -u $USER_ID -g $GROUP_ID $USER_NAME && \
    usermod -a -G fpm $USER_NAME

USER $USER_NAME
