FROM tconcept/tc_php:latest

RUN ln -fs /var/www/html/TConcept-GPAO/docker/php/.zshrc-root ~/.zshrc && \
    ln -fs /var/www/html/TConcept-GPAO/docker/php/php.ini /usr/local/etc/php/php.ini && \
    ln -fs /var/www/html/TConcept-GPAO/docker/php/php-fpm.conf /usr/local/etc/php/php-fpm.conf && \
    touch /var/log/cron.log
RUN apt-get update \
    && apt-get install -y cups-client \
    && apt-get install -y libssh2-1-dev libssh2-1 \
    && pecl install ssh2-1.3.1 \
    && docker-php-ext-enable ssh2
ADD crontab /tmp/crontab

RUN crontab /tmp/crontab && rm /tmp/crontab

ARG GROUP_ID
ARG USER_ID
ARG USER_NAME

RUN groupadd -f -g $GROUP_ID $USER_NAME && \
    groupadd fpm && \
    useradd -u $USER_ID -g $GROUP_ID -d /var/www/html -s /bin/zsh $USER_NAME && \
    usermod -a -G fpm $USER_NAME && \
    usermod -a -G sudo $USER_NAME && \
    echo "$USER_NAME:user" | chpasswd # Mot de passe utilisateur : user

USER $USER_NAME

WORKDIR /var/www/html

RUN wget https://github.com/robbyrussell/oh-my-zsh/raw/master/tools/install.sh -O - | zsh && \
    git clone https://github.com/zsh-users/zsh-syntax-highlighting.git ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-syntax-highlighting && \
    git clone https://github.com/zsh-users/zsh-autosuggestions ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-autosuggestions && \
    ln -fs /var/www/html/TConcept-GPAO/docker/php/.zshrc ~/.zshrc && \
    ln -fs /var/www/html/TConcept-GPAO/docker/php/.bash_aliases ~/.bash_aliases

WORKDIR /var/www/html/TConcept-GPAO
