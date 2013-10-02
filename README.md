berlingame
==========

web based game

install
--------------------------------------------
install apache, PHP a MySQL
change DB connection settings in config/config.php
install DB scripts from db/install.sql
allow write to directories log, include (chmod -R 777 log, chmod -R 777 log)
run

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
There will be warnings from PHP, weird code constructs, names of functions in Czech, ... You would see run of hundreds DB queries on single page reload and much more.

I quickly tried to fix all major troubles which blocked working in newer version of PHP, but there will definitely remain plenty bugs.
