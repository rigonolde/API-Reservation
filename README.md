# API Reservation
## Installation
Cette application utilise un environnement Docker. Vous pouvez lancer l'application avec la commande suivante :
```bash
    docker compose up -d
```
- ##### Placer dans le container qui contient le php
```bash
  docker exec  -it test_php sh
```
- ##### Installer les dépendances du projet
```bash
  composer install
```
- ##### Initialiser la base de donnée et importer les données fixture
```bash
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:fixtures:load
```

## Lancer les tests
```bash
    php bin/phpunit
```

## URL pour accesser à l'application
```bash
    http://127.0.0.1:8080/api/reservations
```