.PHONY: dev up shell refresh image compile

dev:
	docker compose up -d --remove-orphans --build

up:
	docker compose up -d --remove-orphans

shell: up
	docker compose exec app bash

refresh:
	docker compose down
	docker compose build
	docker compose up -d --remove-orphans

build-image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# example: docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api

compile:
	composer dumpautoload
	CEKTA_MODE=compile ./app.php

checks:
	# check code style, or static analyze, or run unit tests, maybe run this target pre merge, runed on build prod