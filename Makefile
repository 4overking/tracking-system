include .env

SHELL = /bin/sh

.DEFAULT_GOAL := help

APP_CONTAINER_NAME := php

docker_bin := $(shell command -v docker 2> /dev/null)
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)

help:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

initialize: ## Start all containers (in background) for development
	$(docker_compose_bin) up -d --build
	$(docker_compose_bin) exec $(APP_CONTAINER_NAME) composer install
	$(docker_compose_bin) exec $(APP_CONTAINER_NAME) bin/console doctrine:schema:update --force

up: ## Start all containers (in background) for development
	$(docker_compose_bin) up -d --build

down: ## Stop all started for development containers
	$(docker_compose_bin) down

console: ## Container console
	$(docker_compose_bin) exec $(APP_CONTAINER_NAME) bash

run_tests: ## Container console
	$(docker_compose_bin) exec $(APP_CONTAINER_NAME) ./vendor/bin/phpunit