# Aliases

## PHP Coding Standards Fixer
alias gpao:fix:code='/var/www/html/TConcept-GPAO/vendor/bin/php-cs-fixer fix'

## PHPStan
alias gpao:stan='/var/www/html/TConcept-GPAO/vendor/bin/phpstan analyse --memory-limit=1G'

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
alias gpao:database:load='php /var/www/html/TConcept-GPAO/bin/console gpao:database:load'
alias gpao:schema:update='php /var/www/html/TConcept-GPAO/bin/console gpao:schema:update'
