.PHONY: dev  shell refresh image build

dev:
	docker compose up --remove-orphans

build:
	docker compose run --rm -it app composer build

migrate:
	docker compose run --rm -it app ./app.php migrate

shell:
	docker compose run --rm -it app sh

refresh:
	docker compose down
	docker compose build

image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# example: docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api
