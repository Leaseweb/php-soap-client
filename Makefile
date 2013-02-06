PKG_NAME=php-soap-client-$(VERSION)
PKG_FRMT=tar.gz
BIN=$(DESTDIR)/usr/bin

build: clean
	php create-phar.php
	chmod +x build/soap_client.phar
clean: prepare
	rm -rf build/*
prepare:
	if [ ! -d build ]; then mkdir build; fi
install:
	install build/soap_client.phar $(BIN)/soap_client
remove:
	rm -f $(BIN)/soap_client
package:
	git archive --format=$(PKG_FRMT) --prefix=$(PKG_NAME)/ $(VERSION) > $(PKG_NAME).$(PKG_FRMT)
help:
	@echo 'Usage: make {build|clean}'
