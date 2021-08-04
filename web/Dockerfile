FROM php:8.0-cli

RUN apt-get update \
    && apt-get install -y \
        git \
        # zip
        libzip-dev \
        # gd
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        # redis
        redis-tools

# Nouvelle syntaxe pour les paramètres (cf. https://github.com/docker-library/php/issues/945)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install zip gd exif

RUN mkdir /usr/paysdufle.fr

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ADD "https://www.random.org/cgi-bin/randbyte?nbytes=10&format=h" skipcache

RUN git clone https://github.com/TeddyBear06/paysdufle.fr-site.git /usr/paysdufle.fr/src

COPY ./run.sh .

RUN chmod +x run.sh

ENTRYPOINT ["./run.sh"]