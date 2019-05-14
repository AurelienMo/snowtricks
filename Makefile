EXEC_APACHE        = sudo service apache2
EXEC_PHP        = php

SYMFONY         = $(EXEC_PHP) bin/console
COMPOSER        = composer
NPM            = npm

VAGRANT = vagrant

##
## ALIAS
## -------
##

ex: xdebug-enable
dx: xdebug-disable
cc: cache-clear
cw: cache-warmup
cs: phpcs
stan: phpstan
cbf: phpcbf
quality: phpcs phpcbf
test: test-functional
coverage: test-functional-coverage


##
## Project
## -------
##

.DEFAULT_GOAL := help

help: ## Default goal (display the help message)
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.PHONY: help

start: ## Start VM.
start: Vagrantfile
	$(VAGRANT) up

stop: ## Stop VM.
stop: Vagrantfile
	$(VAGRANT) halt

##
## Tools
## ------
##
xdebug-enable: ## Enable Xdebug
	sudo phpenmod xdebug
	$(EXEC_APACHE) restart

xdebug-disable: ## Disable Xdebug
	sudo phpdismod xdebug
	$(EXEC_APACHE) restart

cache-clear: ## Clear the cache (by default, the dev env is used)
cache-clear: var/cache
	$(SYMFONY) cache:clear --env=$(or $(ENV), dev)

cache-warmup: ## Cache warmup (by default, the dev env is used)
	$(SYMFONY) cache:warmup --env=$(or $(ENV), dev)

phpcs: ## Run phpcs
phpcs: vendor/bin/phpcs
	vendor/bin/phpcs

phpstan: ## Run phpstan
phpstan: vendor/bin/phpstan
	vendor/bin/phpstan analyze src

phpcbf: ## Run PHPCBF
phpcbf: vendor/bin/phpcbf
	vendor/bin/phpcbf

##
## Manage Dependencies
## ------
##

vendor: ## Install composer dependecies
vendor: composer.lock
	$(COMPOSER) install -n --prefer-dist

new-vendor: ## Require new dependency
new-vendor: composer.json
	$(COMPOSER) require $(DEP)

new-dev-vendor: ## Require new dev dependency
new-dev-vendor: composer.json
	$(COMPOSER) require $(DEP) --dev

remove-vendor: ## Remove dependency
remove-vendor: composer.json
	$(COMPOSER) require $(DEP) --dev

npm-install: ## Install npm depencies
npm-install: package.lock
	$(NPM) install

add-npm: ##  Add package to dependencies
add-npm: package.json
	$(NPM) install $(PACKAGE) --save

dev-npm: ## Add package to dev dependencies
dev-npm: package.json
	$(NPM) install $(PACKAGE) --save-dev

##
## Database
## ------
##
db-diff: ## Generate doctrine diff
db-diff: src/Entity
	$(SYMFONY) doctrine:migrations:diff

db-migr: ## Execute migration
db-migr: src/Domain/DoctrineMigrations
	$(SYMFONY) doctrine:migrations:migrate -n

db-down: ## Down migration
db-down: src/Domain/DoctrineMigrations
	$(SYMFONY) doctrine:migrations:exec --down $(VERSION) -n

db-up: ## Up migration
db-up: src/Domain/DoctrineMigrations
	$(SYMFONY) doctrine:migrations:exec --up $(VERSION) -n

##
## TESTS
## ------
##
test-unit: ## Run phpunit
test-unit: tests
	vendor/bin/phpunit
test-functional: ## Run behat
test-functional: features
	vendor/bin/behat
test-functional-coverage: ## Coverage code behat
test-functional-coverage: features
	vendor/bin/behat --profile=coverage
