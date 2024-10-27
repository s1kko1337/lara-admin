#PHP CLI
FROM php:8.2-cli-alpine3.17 as cli

COPY ./docker/php/cli/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer-2 opcache pdo_pgsql pgsql curl

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1

RUN apk --no-cache add bash

COPY ./ /app

WORKDIR /app

RUN rm -rf /app/vendor \
    && composer --no-ansi --no-interaction install --no-progress

#PHP-FPM
FROM php:8.2-fpm-alpine3.17 as fpm

COPY ./docker/php/fpm/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk --no-cache add postgresql-dev

RUN install-php-extensions opcache pdo_pgsql pgsql curl

RUN apk --no-cache add bash

COPY --from=cli /app /var/www/html

WORKDIR /var/www/html

USER 1000

CMD php-fpm
EXPOSE 9000

#NGINX
FROM nginx:1.20.2-alpine as nginx

COPY docker/nginx/conf.d/* /etc/nginx/conf.d

COPY --from=cli /app/public /var/www/html/web

RUN apk update && apk upgrade && apk add --no-cache bash

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf

EXPOSE 80


#NodeJS
FROM node:18-alpine as nodejs

ARG UID
ARG GID

USER root

RUN if [ $(id -u node) -ne ${UID} ]; then groupmod -g ${GID} node && usermod -u ${UID} node; fi

USER node

WORKDIR /app

CMD sh -c "tail -f /dev/null"