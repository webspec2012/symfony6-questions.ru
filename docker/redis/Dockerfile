# docker/redis/Dockerfile

# REDIS 6.2
# https://github.com/docker-library/redis/blob/master/6.2/Dockerfile
FROM redis:6.2-alpine

# Работа от пользователя
ARG USER_UID
ARG USER_GID
RUN apk add --no-cache shadow
RUN usermod -u ${USER_UID} redis && groupmod -g ${USER_GID} redis
