# Symfony6 Questions (в разработке)

Простой пример реализации сервиса вопросы-ответы.

## Используемые технологии в проекте

* Язык разработки: `PHP 8.1`
* Веб сервер: `nginx + php-fpm`
* Фреймворк: `Symfony 6`
* База данных: `MySql 8`
* Под обработку очередей: `RabbitMQ 3.9`
* Под кэширование и сессии: `Redis 6`

## Установка и настройка через `docker`&`docker-compose`.

Инструкции ниже предполагают, что у вас уже установлены docker&docker-compose.

* Выгрузить код из гита

```bash
sudo mkdir -p /var/www/projects/symfony6-questions.ru && cd $_
sudo chown fastuser:fastuser .
git clone git@github.com:webspec2012/symfony6-questions.ru.git .
```

### Инициализация проекта (в `dev` режиме):

```bash
# Скопировать конфиг и произвести необходимые настройки (для разработки через docker изменений вносить не требуется).
cp app/env.dev.local.example env.local

# Запуск docker контейнеров
sudo docker-compose up --build --force-recreate -d

# Установка вендоров
sudo docker-compose exec -T -u www-data php-fpm sh -c "composer install --no-interaction --no-plugins --no-progress --no-scripts --ansi"

# Применение миграций и загрузка тестовых данных
sudo docker-compose exec -T -u www-data php-fpm sh -c "php bin/console doctrine:migrate --up -n"
sudo docker-compose exec -T -u www-data php-fpm sh -c "php bin/console doctrine:fixtures:load --group=production -n"

# Сброс кеша
sudo docker-compose exec -T -u www-data php-fpm sh -c "php bin/console cache:clear"
```

* Frontend доступен тут: http://127.0.0.1:8110
* Backend доступен тут: http://127.0.0.1:8120

### Инициализация проекта (в `prod` режиме):

```bash
# Скопировать конфиг и произвести необходимые настройки.
cp app/env.prod.local.example env.local

# Запуск docker контейнеров
sudo docker-compose -f docker-compose.prod.yml up --build --force-recreate -d

# Установка вендоров
sudo docker-compose -f docker-compose.prod.yml exec -T -u www-data php-fpm sh -c "composer install --ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader"

# Создание hydrators&proxies для Doctrine ORM
sudo docker-compose -f docker-compose.prod.yml exec -T -u www-data php-fpm sh -c "php bin/console doctrine:generate:hydrators --env=prod"
sudo docker-compose -f docker-compose.prod.yml exec -T -u www-data php-fpm sh -c "php bin/console doctrine:generate:proxies --env=prod"

# Сброс кеша
sudo docker-compose -f docker-compose.prod.yml exec -T -u www-data php-fpm sh -c "php bin/console cache:clear"
```
