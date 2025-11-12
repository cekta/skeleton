up:
	docker compose up -d --remove-orphans

shell: up
	docker compose exec app bash

refresh:
	docker compose down
	docker compose build
	docker compose up -d --remove-orphans

restart:
	docker comppose stop
	docker compose up -d --remove-orphans

image:
	# build docker image ready to deploy

	# rr.yaml must be changed (disable debug and develop mode)