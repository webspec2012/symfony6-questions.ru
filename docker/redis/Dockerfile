# docker/redis/Dockerfile

# REDIS 6
FROM redis:6-alpine

# Работа от пользователя
ARG USER_UID
ARG USER_GID
RUN apk add --no-cache shadow
RUN usermod -u ${USER_UID} redis && groupmod -g ${USER_GID} redis
