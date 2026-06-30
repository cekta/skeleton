MAKEFLAGS += --no-print-directory

.PHONY: dev
dev:
	docker compose up -d --remove-orphans

.PHONY: build
build:
	docker compose run -it --rm --no-deps app composer build

.PHONY: migrate
migrate:
	docker compose exec app composer migrate

.PHONY: rollback
rollback:
	docker compose exec app composer rollback

.PHONY: shell
shell:
	docker compose exec app sh

.PHONY: test
test:
	docker compose run --rm -it app composer test

.PHONY: refresh
refresh:
	docker compose down --remove-orphans -v
	docker compose build
	make build
	make dev
	make migrate

.PHONY: restart
restart:
	docker compose stop
	@make build
	@make dev

.PHONY: image
image:
	docker build --target prod -t cekta-app .
	# maybe set you custom TAG ?
	# you can modify build arg --build-arg RR_LOGS_LEVEL_ARG=debug or overwrite env on runtime!!! see image
	# example: docker run -p 8090:8080 --rm cekta-app:latest # on port 8090 prod api
