# Symfony6 Questions (в разработке)

Простой пример реализации сервиса вопросы-ответы.

## Используемые технологии в проекте

* Язык разработки: `PHP 8.1`
* Веб сервер: `Nginx 1.21 + php-fpm`
* Кэширующий прокси: `Varnish 7.0`
* Фреймворк: `Symfony 6`
* База данных: `MySQL 8` совместно с `Doctrine ORM 2.11`
* Поисковой движок: `ElasticSearch 7.17`
* Кэширование: `Redis 6` & `File`
* Очереди сообщений: `RabbitMQ 3.9`
* Управление процессами воркеров: `Supervisor`
* Шаблонизатор: `Twig 3.3`
* Вёрстка шаблонов: `Bootstrap 5.1` + иконки от `Font Awesome Free 6.0`
* Оптимизация загружаемых изображений: `jpegoptim` + `optipng`
* Сборка ресурсов фронта с использование `webpack`
* Без использования готовых bundle

## Установка и настройка через `docker`&`docker-compose`.

Инструкции ниже предполагают, что у вас уже [установлены docker&docker-compose](./docker/README.md).

* Выгрузить код из гита

```bash
mkdir -p /var/www/projects/symfony6-questions.ru && cd $_
git clone git@github.com:webspec2012/symfony6-questions.ru.git .
```

### Инициализация проекта (в `dev` режиме):

* Выполнить следующие инструкции:

```bash
# Запуск docker контейнеров
sudo docker-compose up --build --force-recreate -d

# Инициализация проекта
sudo docker-compose exec -T -u www-data php-fpm sh -c "make dev"
```

* Прописать в `/etc/hosts` (у себя в компьютере):

```bash
127.0.0.1 symfony6-questions.ru backend.symfony6-questions.ru
```

Современные браузеры не дают заходить на сайты без https, и делают редирект даже в том случае,
если https версии у сайта нет. Для Firefox решение следующее: вводим в адресной строке `about:config`, находим
параметр `browser.urlbar.autoFill` и устанавливаем его в `false`.

* Frontend доступен тут: http://symfony6-questions.ru:8110
* Backend доступен тут: http://backend.symfony6-questions.ru:8110

### Инициализация проекта (в `prod` режиме):

```bash
# Скопировать конфиг и произвести необходимые настройки.
cp app/.env app/.env.local

# Запуск docker контейнеров
sudo docker-compose -f docker-compose.prod.yml up --build --force-recreate -d

# Инициализация проекта
sudo docker-compose -f docker-compose.prod.yml exec -T -u www-data php-fpm sh -c "make prod"
```
