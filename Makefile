.PHONY: clean
build: clean
	bin/compile
	chmod +x soap_client.phar


.PHONY: clean
clean:
	rm -f soap_client.phar
	rm -rf build


# Test everything
.PHONY: test
test: clean
	vendor/bin/phpunit


.PHONY: phpmd
phpmd:
	vendor/bin/phpmd src text codesize,unusedcode,naming,design; [ $$? -eq 2 ] && true


.PHONY: coverage
coverage: clean
	vendor/bin/phpunit --coverage-text


# Install the binary into /usr/local/bin
.PHONY: install
install:
	mkdir -p /usr/local/bin
	cp soap_client.phar /usr/local/bin/soap_client
