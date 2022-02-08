# docker/rabbitmq/Dockerfile

# RabbitMQ 3.9-alpine
# https://github.com/docker-library/rabbitmq/blob/master/3.9/alpine/Dockerfile
FROM rabbitmq:3.9-alpine

# Работа от указанного пользователя
ARG USER_UID
ARG USER_GID

RUN apk add --no-cache shadow
RUN usermod -u ${USER_UID} rabbitmq && groupmod -g ${USER_GID} rabbitmq
