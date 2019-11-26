SHELL:=/bin/bash

help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST) | sort

build: ## Сборка проекта
	docker-compose build

test: ## Тестирование проекта
	@docker-compose run php_sdk bash -c "./vendor/bin/phpunit"

.PHONY: help build test
.DEFAULT_GOAL := help
