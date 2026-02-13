WEB=/var/www/xmlrpc/doc

dist: xmlrpc_php.xml
	-mkdir out
	@echo Jade cmd yet to be rebuilt, staring from xml file and putting output in ./out dir, e.g. jade -t xml -d custom.dsl xmlrpc_php.xml
	mkdir -p ../xmlrpc-${VERSION}/doc
	-cp out/*.css ../xmlrpc-${VERSION}/doc
	-cp out/*.html ../xmlrpc-${VERSION}/doc
	-cp out/*.pdf ../xmlrpc-${VERSION}/doc
	cp xmlrpc_php.xml ../xmlrpc-${VERSION}/doc
	cp Makefile ../xmlrpc-${VERSION}/doc

clean:
	-rm -f out/*.html
	-rm -f out/*.css

install:
	mkdir -p ${WEB}
	cp *.html ${WEB}
	cp *.css ${WEB}
