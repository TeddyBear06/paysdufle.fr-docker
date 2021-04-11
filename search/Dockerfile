FROM php:8-cli

RUN apt-get update \
    && apt-get install -y \
        git \
        # redis
        redis-tools

COPY . /usr/src

WORKDIR /usr/src

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN chmod +x run.sh

ENTRYPOINT ["./run.sh"]