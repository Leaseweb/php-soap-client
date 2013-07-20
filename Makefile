PKG_NAME=php-soap-client-$(VERSION)
PKG_FRMT=tar.gz
BIN=$(DESTDIR)/usr/bin

build: clean
	bin/compile
	chmod +x soap_client.phar

.PHONY: tags
tags:
	ctags -R --PHP-kinds=+cf --exclude=build --exclude=*.phar src/ vendor/ bin/

clean:
	rm -f soap_client.phar

install:
	install soap_client.phar $(BIN)/soap_client

remove:
	rm -f $(BIN)/soap_client

package:
	git archive --format=$(PKG_FRMT) --prefix=$(PKG_NAME)/ $(VERSION) > $(PKG_NAME).$(PKG_FRMT)

help:
	@echo 'Usage: make {build|clean}'
