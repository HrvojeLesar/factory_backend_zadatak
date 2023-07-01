FROM ubuntu:latest

RUN mkdir -p /app

WORKDIR /app

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    lsb-release \
    ca-certificates \
    apt-transport-https \
    software-properties-common \
    php8.1-cli \
    php8.1-common \
    php8.1-imap \
    php8.1-redis \
    php8.1-xml \
    php8.1-zip \
    php8.1-mbstring \
    php8.1-pgsql \
    composer


CMD [ "php", "artisan", "serve", "--host", "0.0.0.0" ]
