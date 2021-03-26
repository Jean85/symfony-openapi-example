build: composer-install code-style-fix psalm phpstan test

composer-install:
	APP_ENV=test composer install --ansi

composer-update:
	composer update --ansi

code-style-check:
	@echo "CODE STYLE  CHECK"
	@echo ""
	vendor/bin/php-cs-fixer fix --ansi --verbose --dry-run

code-style-fix:
	@echo "CODE STYLE FIX"
	@echo ""
	vendor/bin/php-cs-fixer fix --ansi --verbose

phpstan:
	@echo "PHP STAN"
	@echo ""
	vendor/bin/phpstan analyse --ansi

psalm:
	@echo "PSALM"
	@echo ""
	vendor/bin/psalm
	@echo ""

test:
	vendor/bin/phpunit --verbose
