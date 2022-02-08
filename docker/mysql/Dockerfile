# docker/mysql/Dockerfile

# MySQL 8 (from Debian)
# https://github.com/docker-library/mysql/blob/master/8.0/Dockerfile.debian
FROM mysql:8

# Работа от указанного пользователя
ARG USER_UID
ARG USER_GID
RUN usermod -u ${USER_UID} mysql && groupmod -g ${USER_GID} mysql
