build: clean
	php create-phar.php
	chmod u+x build/soap_client.phar
clean: prepare
	rm -rf build/*
prepare:
	if [ ! -d build ]; then mkdir build; fi
help:
	@echo 'Usage: make {build|clean}'
