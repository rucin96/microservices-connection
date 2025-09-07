IMAGE_NAME = vehis-msc:dev

build:
	docker build -t $(IMAGE_NAME) .

bash: build
	docker run -it --rm -v $(PWD):/app -w /app $(IMAGE_NAME) bash

composer-install: build
	docker run -it --rm -v $(PWD):/app -w /app $(IMAGE_NAME) composer install

phpunit: build
	docker run -it --rm -v $(PWD):/app -w /app $(IMAGE_NAME) ./vendor/bin/phpunit tests
