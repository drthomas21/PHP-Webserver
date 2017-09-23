PHP Webserver - Prophpet
=============

### Description

This is an experimental project to see if it is possible to make a fully functional PHP Webserver similar to how Tomcat or NodeJS work. The purpose of this project is to make a web server that allows session variables or other gloabl values to be shared across multiple connected clients to prevent redundant resource creation. For example: When multiple clients connects to a WordPress website, each client will initial a database connection per client; an ideal situation is to allow that one database connection to be shared acrossed all connected clients. Another issue to resolve is the session write locking (well, it is not really an issue, but it can be troublesome to deal with).

### Exit Codes:
0 = Successful Run
1 = Successful Child Run
2 = Cannot parse config file
3 = Missing requirements
4 = Error with forking

### Exception Codes
100 = InvalidClassException
200 = ExpiredTokenException
300 = DataNotFoundException

### Sample Tests (taken offline)
http://json.superlunchvote.com:8888/ - GET, POST, PUT, DELETE
http://json.superlunchvote.com:8888/timestamp/ - GET
http://sse.superlunchvote.com:8888/ - Server Sent Event socket

### Changelog
#### v0.3a
- adding timeout for child webserver procs
- improve response time
- fix issue where parent proc will exit before children procs are finished
