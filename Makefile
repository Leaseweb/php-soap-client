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
	@echo "Version $(version) commited and tagged. You can 'make push' or 'make upload' now :)"


.PHONY: tags
tags:
	ctags -R --PHP-kinds=+cf-v --exclude=build --exclude=*.phar src/ vendor/ bin/ tests/


.PHONY: clean
clean:
	rm -f soap_client.phar
	rm -rf build


# Test everything
.PHONY: test
test: clean
	phpunit -c tests/


.PHONY: coverage
coverage: clean
	phpunit -c tests/ --coverage-html ./build/coverage


# Push to github but run tests first
.PHONY: push
push: test
	git push origin HEAD
	git push origin --tags


# Install the binary into /usr/local/bin
.PHONY: install
install:
	mkdir -p /usr/local/bin
	cp soap_client.phar /usr/local/bin/soap_client
