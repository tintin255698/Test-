FROM php:8.1.4-fpm-alpine3.14

ARG UID=${UID}
ARG GUID=${GUID}

RUN apk --update --no-cache add git
RUN apk add shadow
RUN docker-php-ext-install pdo_mysql
ENV COMPOSER_MEMORY_LIMIT -1
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN userdel -r www-data
RUN groupdel dialout
RUN addgroup -g ${GUID} www-data;
RUN useradd -m -u ${UID} -g ${GUID} www-data;

USER www-data:www-data

CMD composer install ;  php-fpm

EXPOSE 9000
