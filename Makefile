up:
	docker compose up -d --remove-orphans --build

shell: up
	docker compose exec app bash

refresh:
	docker compose down
	docker compose build
	docker compose up -d --remove-orphans

image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api