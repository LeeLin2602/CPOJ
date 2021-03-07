FROM php:7.3-apache

RUN set -ex; \
	apt-get update; 

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

EXPOSE 80
