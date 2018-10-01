# how to execute the tests

1. make sure that you are in the root directory of this plugin
2. copy `.env.example` as `.env` and edit it
3. `docker-compose up -d`
4. `docker-compose exec wordpress bash -c 'bin/install-wp-tests.sh wordpress-test {{USER}} {{PASSWORD}} {{HOST}}'`

then `docker-compose exec wordpress bash -c 'phpunit'`
