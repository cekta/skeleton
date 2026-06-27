.PHONY: dev  shell refresh image build

dev:
	docker compose up -d --remove-orphans

build:
	docker compose exec app composer build

migrate:
	docker compose exec app composer migrate

rollback:
	docker compose up -d --wait --remove-orphans app
	docker compose run --rm -it app composer rollback

shell:
	docker compose exec app sh

test:
	docker compose run --rm -it app composer test

refresh:
	docker compose down --remove-orphans -v
	docker compose build

restart:
	docker compose stop
	@make dev

image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# example: docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api
