build: clean
	bin/compile
	chmod +x soap_client.phar

.PHONY: tags
tags:
	ctags -R --PHP-kinds=+cf --exclude=build --exclude=*.phar

clean:
	rm -f soap_client.phar

install:
	if [ ! -d /usr/local/bin ]; then mkdir /usr/local/bin; fi
	cp -f build/soap_client.phar /usr/local/bin/soap_client

remove:
	rm -f /usr/local/bin/soap_client

help:
	@echo 'Usage: make {build|clean}'
