.PHONY: dev  shell refresh build-image build

dev:
	docker compose up --remove-orphans

shell:
	docker compose run --rm -it app sh

refresh:
	docker compose down
	docker compose build

build-image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# example: docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api

build:
	docker compose run --rm -it app composer dumpautoload