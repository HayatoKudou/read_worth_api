
## for Development
.PHONY: setup lint fmt tes larastan ide-helper
setup: .env
	chmod 777 ./bootstrap/cache
	find ./storage -type d | xargs chmod 777
	composer install

lint:
	$(MAKE) -C ../ lint

fmt:
	$(MAKE) -C ../ fmt

test:
	./vendor/phpunit/phpunit/phpunit --configuration ./phpunit.xml ./tests

larastan:
	./vendor/bin/phpstan analyse

ide-helper:
	php artisan ide-helper:generate
	php artisan ide-helper:models --write --reset
	php artisan ide-helper:meta
	php artisan ide-helper:eloquent

## no .PHONY:
.env:
	cp .env.local .env

