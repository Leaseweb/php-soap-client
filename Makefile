build: clean
	php create-phar.php
	chmod +x build/soap_client.phar
clean: prepare
	rm -rf build/*
prepare:
	if [ ! -d build ]; then mkdir build; fi
install:
	if [ ! -d /usr/local/bin ]; then mkdir /usr/local/bin; fi
	cp -f build/soap_client.phar /usr/local/bin/soap_client
remove:
	rm -f /usr/local/bin/soap_client
help:
	@echo 'Usage: make {build|clean}'
