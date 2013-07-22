PKG_NAME=php-soap-client-$(VERSION)
PKG_FRMT=tar.gz
BIN=$(DESTDIR)/usr/bin

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
	ctags -R --PHP-kinds=+cf-v --exclude=build --exclude=*.phar src/ vendor/ bin/

.PHONY: clean
clean:
	rm -f soap_client.phar

install:
	install soap_client.phar $(BIN)/soap_client

remove:
	rm -f $(BIN)/soap_client

.PHONY: package
package:
	git archive --format=$(PKG_FRMT) --prefix=$(PKG_NAME)/ $(VERSION) > $(PKG_NAME).$(PKG_FRMT)
