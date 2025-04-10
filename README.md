# Event Booking API

## Prerequisites
Git, Docker & Docker Compose installed

## Setting up locally

### Clone the Repository
```
git clone https://github.com/chetan118/event-booking-api.git
cd event-booking-api
```

### Start Docker
```
docker-compose up -d --build
```

### Install Symfony
```
docker-compose exec php composer install
```

### Create the Database
```
docker-compose exec php bin/console doctrine:database:create
```

### Run Migrations
```
docker-compose exec php bin/console doctrine:migrations:migrate
```

### Run Tests
Tests use the `symfony_test` database
```
docker-compose exec php bin/console doctrine:database:create --env=test
docker-compose exec php bin/console doctrine:migrations:migrate --env=test
docker-compose exec php bin/phpunit
```