FROM php:7.0-apache

RUN apt-get update \
    && apt-get install -y subversion mysql-server zlib1g-dev libpng-dev libjpeg-dev \
    && docker-php-ext-configure gd \
    --with-png-dir=/usr \
    --with-jpeg-dir=/usr \
    && docker-php-ext-install gd mysqli pdo_mysql mbstring zip

RUN curl curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar > /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp

# install composer
# https://github.com/composer/docker/blob/edf4f0abf50da5d967408849434b9053a195b65f/1.7/Dockerfile
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /tmp
ENV COMPOSER_VERSION 1.7.2
RUN curl --silent --fail --location --retry 3 --output /tmp/installer.php --url https://raw.githubusercontent.com/composer/getcomposer.org/b107d959a5924af895807021fcef4ffec5a76aa9/web/installer \
    && php -r " \
    \$signature = '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061'; \
    \$hash = hash('SHA384', file_get_contents('/tmp/installer.php')); \
    if (!hash_equals(\$signature, \$hash)) { \
    unlink('/tmp/installer.php'); \
    echo 'Integrity check failed, installer is either corrupt or worse.' . PHP_EOL; \
    exit(1); \
    }" \
    && php /tmp/installer.php --no-ansi --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION} \
    && composer --ansi --version --no-interaction \
    && rm -rf /tmp/installer.php

# php v7.1 or later
# RUN curl --silent --fail --location --retry 3 --output /usr/local/bin/phpunit --url https://phar.phpunit.de/phpunit-7.phar \
#     && chmod +x /usr/local/bin/phpunit

# php v7.0
RUN curl --silent --fail --location --retry 3 --output /usr/local/bin/phpunit --url https://phar.phpunit.de/phpunit-6.phar \
    && chmod +x /usr/local/bin/phpunit

COPY ./php.ini /usr/local/etc/php/

CMD ["apache2-foreground"]
