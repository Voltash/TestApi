RUN app locally

1) Go to app home directory and run:
```
 cp .env.dist .env
```
2) Add configuration for db and app secret

3) Go to app home directory and run:
```
 docker-compose up -d
```
4) Install composer dependencies:
```
 docker-compose exec php sh -c 'composer instal'
```
5) run migration:
```
 docker-compose exec php sh -c 'php bin/console do:mi:mi'
```
6) Generate keys for jwt if first it first run
```
docker-compose exec php sh -c '
    set -e
    apt-get install openssl
    php bin/console lexik:jwt:generate-keypair
'
```
10) In browser go to http://localhost/api/docs
