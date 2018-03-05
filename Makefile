install:
		composer install
make lint:
		composer run-script phpcs -- --standard=PSR2 bin
test:
		composer run-script phpunit test

.PHONY: test
