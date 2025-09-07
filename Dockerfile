FROM php:8.2

RUN apt-get update -y && apt-get install -y git unzip nano \
   librabbitmq-dev \
   libssl-dev \
   && pecl install amqp \
   && docker-php-ext-enable amqp


RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
	php composer-setup.php && \
	php -r "unlink('composer-setup.php');" && \
	mv composer.phar /usr/local/bin/composer

WORKDIR /var/app

ARG DEBIAN_FRONTEND=noninteractive