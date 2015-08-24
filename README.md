PHP Webserver
=============

Description
-----------

This is an experimental project to see if it is possible to make a fully functional PHP Webserver similar to how Tomcat or NodeJS work. The purpose of this project is to make a web server that allows session variables or other gloabl values to be shared across multiple connected clients to prevent redundant resource creation. For example: When multiple clients connects to a WordPress website, each client will initial a database connection per client; an ideal situation is to allow that one database connection to be shared acrossed all connected clients. Another issue to resolve is the session write locking (well, it is not really an issue, but it can be troublesome to deal with).

Requirement
----------

* PHP 5.6+ mulit-threaded
* Sudo privileges

Testing Environment
-------------------

* Kubuntu 15.04 VM
* 64-bit
* RAM: 2048 MB DDR3
* CPU: Intel 4-core i7-2700K @ 3.50GHz
