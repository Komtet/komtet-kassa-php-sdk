SHELL:=/bin/bash

help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort

build: ## Сборка проекта
	docker-compose build

start_php_5: stop  ## Запустить контейнер с php 5
	@docker-compose up -d php_sdk_5

start_php_7: stop  ## Запустить контейнер с php 7
	@docker-compose up -d php_sdk_7

stop:  ## Остановить все контейнеры
	@docker-compose down

cli_php_5: ## Запуск командной строки с php 5
	@docker-compose run php_sdk_5 bash

cli_php_7: ## Запуск командной строки с php 7
	@docker-compose run php_sdk_7 bash

test_php_5: ## Тестирование проекта с php 5
	@docker-compose run php_sdk_5 bash -c "composer test"

test_php_7: ## Тестирование проекта с php 7
	@docker-compose run php_sdk_7 bash -c "composer test"

.PHONY: help build test
.DEFAULT_GOAL := help
