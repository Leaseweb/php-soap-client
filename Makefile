APP_FILE=src/PhpSoapClient/Application.php
current_version=$(shell sed -n "/protected static \$$VERSION/s/^.*'\([^']*\)'.*$$/\1/p" $(APP_FILE))


.PHONY: clean
build: clean
	bin/compile
	chmod +x soap_client.phar


.PHONY: bump
bump:
	@echo "Current version number: $(current_version)"
	@test ! -z "$(version)" || ( echo "[ERROR] Specify a version number: make bump version=$(current_version)" && exit 1 )
	@! git status --porcelain 2> /dev/null | grep -v "^??" || ( echo '[ERROR] Uncommited changes. Commit them first.' && exit 1 )
	@echo "Bumping version $(current_version) to $(version)"
	sed -i'.bak' -e "/protected static \$$VERSION/s/'\([^']*\)'/'$(version)'/" $(APP_FILE)
	rm -f $(APP_FILE).bak
	git add $(APP_FILE)
	git commit -m 'Bumped version number to $(version)'
	git tag -m 'Mark stable release version $(version)' -a $(version)
	@echo "Version $(version) commited and tagged. You can push your changes now :)"


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
	vendor/bin/phpunit -c tests/ --coverage-html ./build/coverage


# Install the binary into /usr/local/bin
.PHONY: install
install:
	mkdir -p /usr/local/bin
	cp soap_client.phar /usr/local/bin/soap_client
