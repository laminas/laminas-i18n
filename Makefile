# Run `make` (no arguments) to get a short description of what is available
# within this `Makefile`.

SHELL=/bin/bash
MKDOCS_IMAGE_ID := $(shell docker images -q laminas/mkdocs | xargs)
PHP_VERSION := 8.2
I18N_IMAGE_NAME := laminas/i18n
I18N_IMAGE_ID := $(shell docker images -q ${I18N_IMAGE_NAME} | xargs)
WORK = /app
DOCKER_PHP=-it -w ${WORK} -v ${PWD}:/app --rm ${I18N_IMAGE_NAME}
MDLINT_FILE = https://raw.githubusercontent.com/laminas/laminas-continuous-integration-action/e321dbdcc74e665512b5d2e8fd9012b3432df897/setup/markdownlint/markdownlint.json

MK_BLUE = echo -e "\033[34m"$(1)"\033[0m"
MK_GREEN = echo -e "\033[32m"$(1)"\033[0m"

MK_INFO = @$(call MK_BLUE,$1)
MK_SUCCESS = @$(call MK_GREEN,$1)

help: ## shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)
.PHONY: help

build-mkdocs-image: documentation-theme ## Build the mkdocs image with necessary dependencies for building the docs
	@$(if ${MKDOCS_IMAGE_ID}, @$(call MK_INFO,"mkdocs image already built"), cd documentation-theme/builder && docker build -t laminas/mkdocs .)
.PHONY: build-mkdocs-image

build-php-image: ## Build the PHP image with necessary dependencies
	@$(if ${I18N_IMAGE_ID},, docker build -t ${I18N_IMAGE_NAME} .)
.PHONY: build-php-image

rebuild-php-image: ## Forcefully rebuild the PHP image
	@$(call MK_INFO,"Rebuilding the PHP Docker Image")
	@docker build --build-arg PHP_VERSION=${PHP_VERSION} --pull -t ${I18N_IMAGE_NAME} .
.PHONY: rebuild-php-image

%-crc _ : WORK=/app/tools/crc
install-crc:
	@docker run $(DOCKER_PHP) composer install
.PHONY: install-crc
bump-crc:
	@$(call MK_INFO,"Bumping Composer Require Checker")
	@docker run $(DOCKER_PHP) composer update -q
	@docker run $(DOCKER_PHP) composer bump -Dq
	@docker run $(DOCKER_PHP) composer update -q
.PHONY: bump-crc

%-infection _ : WORK=/app/tools/infection
install-infection:
	@docker run $(DOCKER_PHP) composer install
.PHONY: install-infection
bump-infection:
	@$(call MK_INFO,"Bumping Infection")
	@docker run $(DOCKER_PHP) composer update -q
	@docker run $(DOCKER_PHP) composer bump -Dq
	@docker run $(DOCKER_PHP) composer update -q
.PHONY: bump-infection

%-rector _ : WORK=/app/tools/rector
install-rector:
	@docker run $(DOCKER_PHP) composer install
.PHONY: install-rector
bump-rector:
	@$(call MK_INFO,"Bumping rector")
	@docker run $(DOCKER_PHP) composer update -q
	@docker run $(DOCKER_PHP) composer bump -Dq
	@docker run $(DOCKER_PHP) composer update -q
.PHONY: bump-rector

%-unused _ : WORK=/app/tools/unused
install-unused:
	@docker run $(DOCKER_PHP) composer install
.PHONY: install-unused
bump-unused:
	@$(call MK_INFO,"Bumping unused")
	@docker run $(DOCKER_PHP) composer update -q
	@docker run $(DOCKER_PHP) composer bump -Dq
	@docker run $(DOCKER_PHP) composer update -q
.PHONY: bump-unused

install: install-crc install-infection install-rector install-unused ## Install composer dependencies
	@$(call MK_INFO,"Installing composer dependencies")
	@composer install
.PHONY: install

bump: bump-crc bump-infection bump-rector bump-unused ## Update dependencies and bump development dependency versions
	@$(call MK_INFO,"Bumping development dependencies and refreshing composer lock")
	@docker run $(DOCKER_PHP) composer update -q
	@docker run $(DOCKER_PHP) composer bump -Dq
	@docker run $(DOCKER_PHP) composer update -q
.PHONY: bump

#
# Docs Related Targets
#
docs-lint: .markdownlint.json ## Lint documentation
	@$(call MK_INFO,"Linting documentation files")
	@docker run -it -w /app -v ${PWD}:/app --rm davidanson/markdownlint-cli2 "docs/**/*.md" README.md
.PHONY: docs-lint

check-links: ## Check documentation links
	@$(call MK_INFO,"Checking links in documentation files")
	@docker run -it -w /app -v ${PWD}:/app --rm lycheeverse/lychee "docs/**/*.md" README.md
.PHONY: check-links

.markdownlint.json: ## Fetch the most recent settings for Markdown lint
	@$(call MK_INFO,"Fetching markdown lint configuration")
	@curl -o .markdownlint.json ${MDLINT_FILE}

documentation-theme: ## fetch the documentation theme repo
	@$(call MK_INFO,"Fetching documentation theme resources")
	@git clone git@github.com:laminas/documentation-theme.git

docs: build-mkdocs-image ## build the docs using a Docker container
	@$(call MK_INFO,"Building documentation")
	@docker run -it -w /app -v ${PWD}:/app --rm laminas/mkdocs ./documentation-theme/build.sh -u https://www.example.com
	@$(call MK_SUCCESS,"file://${PWD}/docs/html/index.html")
.PHONY: docs

#
# PHP Tooling
#

set-baseline: ## Expand the Psalm baseline with current issues
	@$(call MK_INFO,"Resetting the Psalm baseline")
	@docker run $(DOCKER_PHP) vendor/bin/psalm --no-cache --set-baseline=psalm-baseline.xml
.PHONY: set-baseline

update-baseline: ## Remove resolved issues from the baseline
	@$(call MK_INFO,"Updating the Psalm baseline")
	@docker run $(DOCKER_PHP) vendor/bin/psalm --no-cache --update-baseline
.PHONY: update-baseline

sa: ## Run static analysis
	@$(call MK_INFO,"Running static analysis")
	@docker run $(DOCKER_PHP) vendor/bin/psalm --no-cache
.PHONY: sa

cs: ## Run coding standards checks
	@$(call MK_INFO,"Checking coding standards")
	@docker run $(DOCKER_PHP) vendor/bin/phpcs
.PHONY: cs

cs-fix: ## Fix coding standards violations
	@$(call MK_INFO,"Fixing coding standards violations")
	@docker run $(DOCKER_PHP) vendor/bin/phpcbf
.PHONY: cs-fix

test: ## Run tests
	@$(call MK_INFO,"Running Tests")
	@docker run $(DOCKER_PHP) vendor/bin/phpunit
.PHONY: test

composer-checks: ## Dump the composer autoloader
	@$(call MK_INFO,"Validating composer.json and dumping the autoloader")
	@docker run $(DOCKER_PHP) composer validate --strict
	@docker run $(DOCKER_PHP) composer dump-autoload --strict-psr --optimize
.PHONY: composer-checks

composer-require-checker: ## Check for symbols from un-declared dependencies
	@$(call MK_INFO,"Checking for undeclared dependencies")
	@docker run $(DOCKER_PHP) tools/crc/vendor/bin/composer-require-checker check --config-file=tools/crc/config.json
.PHONY: composer-require-checker

mutants: ## Run mutation tests
	@$(call MK_INFO,"Running Mutation Tests")
	@docker run $(DOCKER_PHP) tools/infection/vendor/bin/roave-infection-static-analysis-plugin \
 		--configuration=.infection.json5.dist \
 		--psalm-config=psalm.xml
.PHONY: mutants

unused: ## Run composer-unused
	@$(call MK_INFO,"Checking for unused dependencies")
	@docker run $(DOCKER_PHP) tools/unused/vendor/bin/composer-unused
.PHONY: unused

rector: ## Run Rector and show the diff
	@$(call MK_INFO,"Checking for syntax consistency with rector")
	@docker run $(DOCKER_PHP) tools/rector/vendor/bin/rector process --dry-run -c tools/rector/rector.php
.PHONY: rector

rector-fix: ## Apply Rector changes
	@$(call MK_INFO,"Fixing syntax inconsistencies with rector")
	@docker run $(DOCKER_PHP) tools/rector/vendor/bin/rector process -c tools/rector/rector.php
.PHONY: rector-fix

qa: composer-checks cs test sa composer-require-checker unused rector docs-lint check-links ## Run all QA checks

clean: ## Delete caches and docs-build assets
	@$(call MK_INFO,"Cleaning up")
	@rm -rf documentation-theme
	@rm -rf docs/html
	@rm -f .phpcs-cache
	@rm -f .phpunit.result.cache
	@rm -rf .phpunit.cache
	@rm .markdownlint.json

#
# Targets for CI
#

rector-ci:
	cd tools/rector && composer install
	tools/rector/vendor/bin/rector process --dry-run --output-format=github -c tools/rector/rector.php
.PHONY: rector-ci

require-checker-ci:
	cd tools/crc && composer install
	tools/crc/vendor/bin/composer-require-checker check --config-file=tools/crc/config.json
.PHONY: require-checker-ci

unused-ci:
	cd tools/unused && composer install
	tools/unused/vendor/bin/composer-unused --output-format=github
.PHONY: unused-ci

infection-ci:
	cd tools/infection && composer install
	tools/infection/vendor/bin/roave-infection-static-analysis-plugin \
		--configuration=.infection.json5.dist \
		--psalm-config=psalm.xml
.PHONY: unused-ci
