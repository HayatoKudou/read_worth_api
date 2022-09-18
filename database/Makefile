SQLDEF_VERSION = v0.12.7

# ubuntu: linux_arm64.tar.gz
setup: SQLDEF_PLATFORM=darwin_amd64.zip
setup:
	curl -sSfL https://github.com/k0kubun/sqldef/releases/download/$(SQLDEF_VERSION)/mysqldef_$(SQLDEF_PLATFORM) -o mysqldef.zip
	unzip mysqldef.zip -d ./bin
	rm mysqldef.zip

seed:
	mysql -h 127.0.0.1 -P 3307 -u root -proot read_worth < dev_seed.sql

dryrun:
	./bin/mysqldef -P 3307 -u root -p root read_worth --dry-run < schema.sql

apply:
	./bin/mysqldef -P 3307 -u root -p root read_worth < schema.sql
