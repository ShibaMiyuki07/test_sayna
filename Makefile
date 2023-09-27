
composer.lock: composer.json
	$(COMPOSER) composer update --prefer-lowest
	touch composer.lock

vendor: composer.lock
	$(COMPOSER) composer install
	touch vendor

example: vendor
	$(PHP) php example/example.php
