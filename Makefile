.PHONY: dev  shell refresh image build

dev:
	docker compose up --remove-orphans

build:
	docker compose run --rm -it app composer build

shell:
	docker compose run --rm -it app sh

test:
	docker compose run --rm -it app composer test

refresh:
	docker compose down --remove-orphans
	docker compose build

restart:
	docker compose stop
	@make dev

image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# example: docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api
