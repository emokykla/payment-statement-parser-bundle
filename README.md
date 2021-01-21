# payment-statement-parser-bundle

## Development
- mount source to container
````shell
ln -s docker-compose.linux.yaml docker-compose.override.yaml
````
- start container
```shell
docker-compose up -d
```
- execute command in php container
```shell
docker-compose exec php your_command
# e.g.
docker-compose exec php composer install
```

## Testing
```shell
docker-compose exec php vendor/phpunit/phpunit/phpunit
```

## Testing in CI/only source in container
- build docker
```shell
docker-compose build php
```
- run phpunit in container
```shell
docker-compose -f docker-compose.yaml run php vendor/phpunit/phpunit/phpunit
```
