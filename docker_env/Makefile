SHELL:=/bin/bash

help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort

build: ## Сборка проекта
	docker-compose build

start: stop  ## Запустить контейнер
	@docker-compose up -d php_sdk

stop:  ## Остановить все контейнеры
	@docker-compose down

cli: build  ## Запуск командной строки
	@docker-compose run php_sdk bash

# test: ## Запуск тестов
# 	@vendor/bin/phpunit tests

test: ## Тестирование проекта
	@docker-compose run php_sdk bash -c "composer test"

.PHONY: help build test
.DEFAULT_GOAL := help
