
# Mon projet de dev symfony

##	Prérequis

1. PHP version 8.1.0
2. MySQL
3. Symfony version 6.0 minimum
4. Composer
5. Npm

##	Installation
Après avoir cloné le projet

Exécutez la commande ``cd projetDev`` pour vous rendre dans le dossier depuis le terminal.

Ensuite, dans l'ordre taper les commandes dans votre terminal :

- 1 ``composer install`` afin d'installer toutes les dépendances composer du projet.

- 2 ``npm install``      afin d'installer toutes les dépendances npm du projet.

- 3 installer la base de donnée MySQL.
-
Pour paramétrer la création de votre base de donnée, rdv dans le fichier .env du projet, et modifier la variable d'environnement selon vos paramètres :

``DATABASE_URL=mysql://User:Password@127.0.0.1:3306/nameDatabasse?serverVersion=5.7``

Puis exécuter la création de la base de donnée avec la commande : ``symfony console doctrine:database:create``


- 4 Exécuter la migration en base de donnée :                                        ``symfony console doctrine:migration:migrate``

- 5 Modifier la clé API dans la class Steam()

- 5 Vous pouvez maintenant accéder à votre portfolio en vous connectant au serveur : ``symfony server:start``



## Dépendance



## Description

Ce projet a pour but de montrer mes connaissances dans le développement avec symfony.


