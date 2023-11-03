# Makefile

# Définition de la commande par défaut quand on exécute 'make' sans arguments
all: rebuild

# Définition de la tâche 'build' pour construire les services
build:
	docker compose build

# Définition de la tâche 'up' pour lancer les services en mode détaché
up:
	docker compose up -d

yarndev:
	docker exec tconcept_gpao_php zsh -c '\
		yarn dev \
    '
# Définition de la tâche 'down' pour arrêter et supprimer les conteneurs, réseaux, images et volumes
down:
	docker compose down

# Définition de la tâche 'rebuild' pour reconstruire les services
rebuild: down build up

# Commande pour se connecter au conteneur et exécuter les commandes
setup:
	docker exec tconcept_gpao_php zsh -c '\
		composer install && \
		yarn && \
		php -d memory_limit=-1 /var/www/html/TConcept-GPAO/bin/console gpao:database:load && \
		yarn dev \
	'

# Commande pour se connecter au conteneur et exécuter la commande doctrine:migrations:diff
diff:
	docker exec tconcept_gpao_php zsh -c '\
		php -d memory_limit=-1 /var/www/html/TConcept-GPAO/bin/console doctrine:migrations:diff \
	'

# Commande pour se connecter au conteneur et exécuter la commande doctrine:migrations:migrate
migrate:
	docker exec tconcept_gpao_php zsh -c '\
		php -d memory_limit=-1 /var/www/html/TConcept-GPAO/bin/console doctrine:migrations:migrate \
	'

# Commande pour se connecter au conteneur et exécuter la commande doctrine:fixtures:load
clear:
	docker exec tconcept_gpao_php zsh -c '\
		php -d memory_limit=-1 /var/www/html/TConcept-GPAO/bin/console cache:clear \
	'

.PHONY: all build up down rebuild
