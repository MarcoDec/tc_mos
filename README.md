# TConcept-GPAO

## Installation

Le projet est prévu pour s'installer sur un système d'exploitation en base Linux. Il est possible sur Windows d'utiliser
des outils comme le [WSL 2](https://docs.microsoft.com/fr-fr/windows/wsl/install).

### Alias

Certains alias ont été définis pour rendre plus pratique la gestion des conteneurs Docker. Pour les utiliser&nbsp;:

1. vérifiez que les alias présents dans le fichier [`.bash_aliases`](./.bash_aliases) n'entrent pas en conflit avec vos
   propres alias&nbsp;;
2. ajoutez les lignes suivantes dans votre fichier de configuration de votre shell (par exemple `~/.bashrc` si vous
   utilisez BASH)&nbsp;:

```sh
# Aliases TConcept-GPAO
if [ -f [chemin vers le clone du projet]/.bash_aliases ]; then
    . [chemin vers le clone du projet]/.bash_aliases
fi
```

### Conteneurs Docker

Depuis la racine du projet, exécutez la commande `docker:recreate`.

### Composer

Exécutez la commande `docker:php` pour entrer dans le conteneur `tconcept_gpao_php`. Une fois dans le conteneur,
exécutez la commande `composer install` pour installer les différentes dépendances.

### Base de données

Pour charger la base de données, exécutez la commande `gpao:database:load`.

### Vite & Vue

La partie *front* est développée avec le *framework* Vue.js. Les différents fichiers composants les *assets* sont
transcompilés grâce au *bundler* Vite.

```sh
yarn # Installe les dépendances
yarn build # Compile les assets en mode production

yarn dev # À utiliser pour lancer le serveur de développement
```

### PhpStorm

Une fois le projet installé, vous pouvez l'ouvrir avec votre IDE. Attention, sous PhpStorm, l'installation de PHPStan
peut poser des problèmes d'indexation. Pour les résoudre, il faut ignorer les fichiers `.phar`&nbsp;:<br/>
![PhpStorm_PHPStan.PNG](./doc/PhpStorm_PHPStan.PNG)

## Conteneurs

Différents conteneurs Docker sont utilisés pour les différents services du projet&nbsp;:

- `tconcept_gpao_apache`&nbsp;: conteneur responsable d'Apache 2, le serveur HTTP accessible grâce
  à [http://localhost:8000](http://localhost:8000). C'est ici que l'on peut utiliser l'application&nbsp;;
- `tconcept_gpao_mysql`&nbsp;: conteneur responsable de MySQL, le système de gestion de base données&nbsp;;
- `tconcept_gpao_php`&nbsp;: conteneur responsable de PHP, pour gérer la version du langage utilisé et pour exécuter les
  différentes commandes Symfony&nbsp;;
- `tconcept_gpao_phpmyadmin`&nbsp;: conteneur responsable de phpMyAdmin, pour avoir une interface de la base de données,
  accessible grâce à [http://localhost:8080](http://localhost:8080).

## Cron

Des crons peuvent être définis grâce à l'attribut `App\Attributes\CronJob`. Les commandes ensuite associées sont les
suivantes&nbsp;:

```sh
gpao:cron --scan # Analyse les commandes et créer les CRON en base de données.
gpao:cron # Lance les CRON.
```

## Devises

Le taux de change des devises est mis à jour par une tâche cron selon la commande `gpao:currency:rate`.

## Qualité du code

Pour modifier et contrôler le code quant aux standards définis pour le projet, utilisez la
commande&nbsp;: `gpao:fix:code`.
