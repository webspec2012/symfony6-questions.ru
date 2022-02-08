# docker/nginx/Dockerfile

# Nginx 1.21 (from Alpine)
# https://github.com/nginxinc/docker-nginx/blob/master/mainline/alpine/Dockerfile
FROM nginx:1.21-alpine

# Работа от указанного пользователя
ARG USER_UID
ARG USER_GID
RUN apk add --no-cache shadow
RUN usermod -u ${USER_UID} nginx && groupmod -g ${USER_GID} nginx

# Рабочая директория
WORKDIR /var/www/

# Открытие нужных портов
EXPOSE 8110 8120
