SHELL := /bin/bash

migrate:
	composer migrate
.PHONY: migrate

fixtures:
	composer fixtures
.PHONY: fixtures

prod:
	composer install --ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
	composer require symfony/runtime
	composer dump-autoload --no-dev --classmap-authoritative
	composer migrate
	php bin/console doctrine:generate:hydrators --env=prod
	php bin/console doctrine:generate:proxies --env=prod
	composer app-cache-clear
.PHONY: prod

dev:
	composer install --no-interaction --no-plugins --no-progress --no-scripts --ansi
	composer require symfony/runtime
	composer migrate
	composer fixtures
.PHONY: dev

tests: export APP_ENV=test
tests:
	composer install --no-interaction --no-plugins --no-progress --no-scripts --ansi
	composer require symfony/runtime
	composer app-cache-clear
	composer migrate
	composer fixtures
	composer tests
.PHONY: tests

psalm:
	composer psalm
.PHONY: psalm
