dev: compile
	docker compose up --build --remove-orphans

sh:
	docker compose run --rm app sh

compile: da
	docker compose run --rm app ./vendor/bin/cekta

update: 
	docker compose run --rm app composer update
da:
	docker compose run --rm app composer dumpautoload
image:
	# build docker image ready to deploy