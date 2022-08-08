SQLDEF_VERSION = v0.12.7
SQLDEF_PLATFORM = darwin_amd64

setup:
	curl -sSfL https://github.com/k0kubun/sqldef/releases/download/$(SQLDEF_VERSION)/mysqldef_$(SQLDEF_PLATFORM).zip -o mysqldef.zip
	unzip mysqldef.zip -d ./bin
	rm mysqldef.zip

dryrun:
	./bin/mysqldef -P 3307 -u root -p root read_worth --dry-run < schema.sql

apply:
	./bin/mysqldef -P 3307 -u root -p root read_worth < schema.sql
