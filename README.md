# Call-system

## Getting Started

These instructions will make sure that you have a copy of the project running on your local machine for development and testing purposes.

### Prerequisites

What do you need to install?

### Installing

- Clone the project
- Check the PHP version, a minimum of 5.6 is required
After that, run the following commands:
composer install - If you don't have the composer: php -d memory_limit=3800m composer.phar install(Download composer.phar in the project folder - https://getcomposer.org/download)
php app/console assets:install
php app/console fos:js-routing:dump
php app/console assetic:dump --force
php app/console doctrine:schema:update --force

## The project is docked, if you work with the docker, just run the following command: docker-compose up -d

- And run these three commands inside the container:
php app/console assets:install
php app/console fos:js-routing:dump
php app/console assetic:dump --force

- To access the container, simply rotate: docker-compose exec php sh