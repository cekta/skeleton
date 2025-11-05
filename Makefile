install:
	docker compose run --rm app composer install
dev: install
	docker compose up --build --remove-orphans

sh:
	docker compose run --rm app sh

update: 
	docker compose run --rm app composer update
da:
	docker compose run --rm app composer dumpautoload
image:
	# build docker image ready to deploy

	# rr.yaml must be changed (disable debug and develop mode)