ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm-alpine

###
## User
###
# change user and group ids
RUN apk add --no-cache --virtual .shadow-deps shadow && \
    usermod -u 1000 www-data && \
    groupmod -g 1000 www-data && \
    apk del .shadow-deps && \
    echo end

####
### Dev utils
####

RUN apk add --no-cache bash

####
### Php dependencies
####


##
# Composer
##
COPY --from=composer:2.0.8 /usr/bin/composer /usr/bin/composer

###
## App
###
WORKDIR /srv/app
RUN chown www-data:www-data /srv/app
USER www-data

# composer cache
COPY --chown=www-data:www-data composer.json .
COPY --chown=www-data:www-data composer.lock .
# debug with: dc run php composer config --global --list
# cache composer, will be installed once more after copying all code
RUN set -eux; composer install --prefer-dist --no-progress --no-scripts --no-interaction

# copy source
COPY --chown=www-data:www-data . .

# install composer and execute scripts
RUN set -eux; \
	mkdir -p var/cache var/log && \
	composer install --prefer-dist --no-progress --no-scripts --no-interaction && \
	composer dump-autoload --classmap-authoritative && \
	composer run-script post-install-cmd && \
	sync
