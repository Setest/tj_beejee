#!make
SHELL = /bin/sh

include ./docker/.env

export $(shell sed 's/=.*//' ./docker/.env)

DOCKER_COMPOSE = docker-compose -f ./docker/docker-compose.yaml
DOCKER_EXEC_APP = docker exec -it ${PROJECT_NAME}_php
PHP_RUNNER:=export PHP_IDE_CONFIG=\"$(PHP_IDE_CONFIG)\" XDEBUG_SESSION=1 && php
DOCKER_EXEC_APP_PHP = docker exec -it ${PROJECT_NAME}_php bash -c
COMPOSER = ${DOCKER_EXEC_APP} composer

# Misc
.DEFAULT_GOAL=help
.PHONY: help install pre-install down build up post-install restart ps test bash

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help:   ## Show this help.
	@grep -E '^([a-zA-Z_-]+:.*?##)|(^##)[^#]*$$' Makefile \
       | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-25s\033[0m %s\n", $$1, $$2}' \
       | sed -e 's/\[32m##\(.*\)$/\1/\[33m\n/g' \
       | sed -e 's/:.*\##//'

install: pre-install down build up post-install ## Create and start docker hub

pre-install:
	@echo --- Pre-install ---

post-install: composer-install composer-dump-env-dev

build: ## Build containers
	${DOCKER_COMPOSE} build

restart: down up ##
up: ## Start the docker hub
	${DOCKER_COMPOSE} up -d
down stop: ## Stop the docker hub
	${DOCKER_COMPOSE} down
ps: ##
	${DOCKER_COMPOSE} ps

## ——  Working with migrations  ——————————————————————————————————
bash: ## Entering inside docker PHP container
	${DOCKER_EXEC_APP} bash
test: ## Run unit tests
	${DOCKER_EXEC_APP_PHP} "${PHP_RUNNER} ./vendor/bin/phpunit"

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)
composer-install: ##
	$(COMPOSER) install --ignore-platform-reqs \
                        --no-interaction \
                        --no-scripts
composer-clear-cache: ##
	$(COMPOSER) clear-cache
composer-dump-autoload: ## Update the composer autoloader because of new classes in a classmap package
	$(COMPOSER) dump-autoload
composer-update: ##
	$(COMPOSER) update
composer-update-lock: ##
	$(COMPOSER) update --lock