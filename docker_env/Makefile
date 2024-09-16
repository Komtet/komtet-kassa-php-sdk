SHELL:=/bin/bash

help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort

build: ## Сборка проекта
	docker-compose build

start_php_7: stop  ## Запустить контейнер с php 7
	@docker-compose up -d php_sdk_7

start_php_8: stop  ## Запустить контейнер с php 8
	@docker-compose up -d php_sdk_8

stop:  ## Остановить все контейнеры
	@docker-compose down

cli_php_7: ## Запуск командной строки с php 7
	@docker-compose run php_sdk_7 bash

cli_php_8: ## Запуск командной строки с php 8
	@docker-compose run php_sdk_8 bash

test_php_7: ## Тестирование проекта с php 7
	@docker-compose run php_sdk_7 bash -c "composer test"

test_php_8: ## Тестирование проекта с php 8
	@docker-compose run php_sdk_8 bash -c "composer test"

.PHONY: help build test
.DEFAULT_GOAL := help
