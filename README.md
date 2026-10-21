berlingame
==========

web based game

install
--------------------------------------------
install Apache, PHP 8.4 with mysqli, and MySQL

change DB connection settings in config/config.php

install DB scripts from db/install.sql

allow write to directories log, include (chmod -R 777 log, chmod -R 777 include)

Docker / PHP upgrade
--------------------------------------------
The server builds from the public official `php:8.4-apache` image and adds only
the mysqli extension. PHP 8.4 receives security support through December 2028:
https://www.php.net/supported-versions.php

Set the server volume path in `docker/docker-compose.yml` for your checkout. When
using the Compose database, set `$DB_SERVER = 'db'`, `$DB_PASS = 'password'`,
and the installed database name in `config/config.php`.

The local Compose file uses MySQL 8.4 with a `db-data` folder mount. Its SQL mode
permits the legacy schema's zero dates and inserts that omit fields without
defaults. This local database is separate from the production setup below.

For a new, empty database, start MySQL and import the schema once after it is ready:

```sh
docker compose -f docker/docker-compose.yml up -d db
docker compose -f docker/docker-compose.yml exec -T -e MYSQL_PWD=password \
  db mysql -uroot < db/install.sql
```

Rebuild with the latest PHP 8.4 patch release and replace only the PHP service
(the database must already be running):

```sh
docker compose -f docker/docker-compose.yml build --pull server
docker compose -f docker/docker-compose.yml up -d --no-deps server
```

The game is available at http://localhost:3333. No database migration is required
for the PHP upgrade.

From the directory containing that rendered Compose file, update only PHP:


```sh
docker compose -f docker/docker-compose.yml run --rm \
  -e DB_HOST=db -e DB_PASSWORD=password -e DB_NAME=berlin \
  server php tests/php_compatibility.php
```

how to export DB
--------------------------------------------
`docker exec -ti berlingame-compose_db_1 /bin/bash`
`mysqldump -u root -p -B  d4355_berlin > export.sql`
`docker cp berlingame-compose_db_1:/export.sql .`


run
--------------------------------------------
just register and play. In database is one admin account and one player account.

login: admin password: heslo

login player password: heslo

If you want to do "midnight's calclulation" of new turns, just run script 
    
    prepocet.php?heslo=heslo

(if you want to run it again, you will have to delete file log/prepocet.second.lock)

Enjoy!

ABOUT CODE
--------------------------------------------
This was one of my first web pages, so it is almost example of "how not to code". 
There will be warnings from PHP, weird code constructs, names of functions in Czech, frames, multiple code entries, ... You will see run of hundreds DB queries on single page reload and much more.

I quickly tried to fix all major troubles which blocked working in newer version of PHP, but there will definitely remain plenty of bugs.
