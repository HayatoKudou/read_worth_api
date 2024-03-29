.PHONY: default
default: docker/up

# for Development
.PHONY: lint fmt test docker/*
lint:
	yarn prettier --check "**/*.php"

fmt:
	./src/vendor/bin/php-cs-fixer fix ./src -v --allow-risky=yes

test:
	yarn dredd
	$(MAKE) docker/exec command="make test"

docker/setup:
	docker build --pull --tag read_worth_backend src/.
	$(MAKE) docker/up
	$(MAKE) docker/exec docker_option="-T" command="make setup"

docker/up:
	docker-compose up -d

docker/stop:
	docker-compose stop

docker/down:
	docker-compose down

docker/exec: docker_option=
docker/exec: service=app
docker/exec: command=bash
docker/exec:
	docker-compose exec $(docker_option) $(service) $(command)
