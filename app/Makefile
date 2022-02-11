SHELL := /bin/bash

migrate:
	php bin/console doctrine:migrations:migrate -n
.PHONY: migrate

fixtures:
	php bin/console doctrine:fixtures:load -n
.PHONY: fixtures

prod:
	composer install --ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader
	composer dump-autoload --no-dev --classmap-authoritative
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:generate:hydrators --env=prod
	php bin/console doctrine:generate:proxies --env=prod
	php bin/console cache:clear -n
.PHONY: prod

dev:
	composer install --no-interaction --no-plugins --no-progress --no-scripts --ansi
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:fixtures:load -n
	php bin/console cache:clear
.PHONY: dev

tests: export APP_ENV=test
tests:
	php bin/console cache:clear -n
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:fixtures:load -n
	php bin/phpunit --testdox
.PHONY: tests

psalm:
	php vendor/bin/psalm --output-format=xml | xsltproc vendor/roave/psalm-html-output/psalm-html-output.xsl - > public/psalm-html.html
.PHONY: psalm
