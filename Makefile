build: clean
	php create-phar.php
	chmod u+x build/soap_client.phar
clean:
	rm -rf build/*
help:
	@echo 'Usage: make {build|clean}'
