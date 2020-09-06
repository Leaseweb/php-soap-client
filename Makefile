.PHONY: clean
build: clean
	mkdir -p build
	box compile


.PHONY: clean
clean:
	rm -f soap_client.phar
	rm -rf build


# Test everything
.PHONY: test
test: clean
	vendor/bin/phpunit


.PHONY: coverage
coverage: clean
	vendor/bin/phpunit --coverage-text


.PHONY: install
install:
	mkdir -p /usr/local/bin
	cp soap_client.phar /usr/local/bin/soap_client
