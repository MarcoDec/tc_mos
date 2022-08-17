# Aliases

## Composer
alias composer:prod='composer install --no-dev --optimize-autoloader'

## Symfony
### Cache
alias cache:clear='php /var/www/html/TConcept-GPAO/bin/console cache:clear'
### Debug
alias debug:autowiring='php /var/www/html/TConcept-GPAO/bin/console debug:autowiring'
alias debug:config='php /var/www/html/TConcept-GPAO/bin/console debug:config'
alias debug:container='php /var/www/html/TConcept-GPAO/bin/console debug:container'
alias debug:event-dispatcher='php /var/www/html/TConcept-GPAO/bin/console debug:event-dispatcher'
alias debug:router='php /var/www/html/TConcept-GPAO/bin/console debug:router'
### GPAO
alias gpao:cron='php /var/www/html/TConcept-GPAO/bin/console gpao:cron'
alias gpao:currency:rate='php /var/www/html/TConcept-GPAO/bin/console gpao:currency:rate'
alias gpao:database:load='php -d memory_limit=1G /var/www/html/TConcept-GPAO/bin/console gpao:database:load'
alias gpao:fixtures:load='php /var/www/html/TConcept-GPAO/bin/console gpao:fixtures:load'
alias gpao:schema:update='php /var/www/html/TConcept-GPAO/bin/console gpao:schema:update'
### Workflow
dump_workflow() {
    php bin/console workflow:dump $1 | dot -Tpng -o "$1.png"
}
alias dump:workflow='dump_workflow'

## PHP Coding Standards Fixer
gpao_fix_code() {
    cache:clear && \
    /var/www/html/TConcept-GPAO/vendor/bin/php-cs-fixer fix && \
    cache:clear && \
    /var/www/html/TConcept-GPAO/vendor/bin/rector process && \
    cache:clear && \
    /var/www/html/TConcept-GPAO/vendor/bin/phpstan analyse --memory-limit=1G && \
    cache:clear && \
    php /var/www/html/TConcept-GPAO/bin/phpunit
}
alias gpao:fix:code='gpao_fix_code'
