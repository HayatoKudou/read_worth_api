SQLDEF_VERSION = v0.12.7
SQLDEF_PLATFORM = darwin_amd64

setup-local:
	curl -sSfL https://github.com/k0kubun/sqldef/releases/download/$(SQLDEF_VERSION)/mysqldef_darwin_amd64.zip -o mysqldef.zip
	unzip mysqldef.zip -d ./bin
	rm mysqldef.zip

setup-prod:
	curl -sSfL https://github.com/k0kubun/sqldef/releases/download/$(SQLDEF_VERSION)/mysqldef_linux_amd64.tar.gz -o mysqldef.tar.gz
	tar -zxvf mysqldef.tar.gz -C ./bin
	rm mysqldef.tar.gz

seed:
	mysql -h 127.0.0.1 -P 3307 -u root -proot read_worth < dev_seed.sql

dryrun:
	./bin/mysqldef -P 3307 -u root -p root read_worth --dry-run < schema.sql

apply:
	./bin/mysqldef -P 3307 -u root -p root read_worth < schema.sql
