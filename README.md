PHP Webserver - Prophpet
=============

Description
-----------

This is an experimental project to see if it is possible to make a fully functional PHP Webserver similar to how Tomcat or NodeJS work. The purpose of this project is to make a web server that allows session variables or other gloabl values to be shared across multiple connected clients to prevent redundant resource creation. For example: When multiple clients connects to a WordPress website, each client will initial a database connection per client; an ideal situation is to allow that one database connection to be shared acrossed all connected clients. Another issue to resolve is the session write locking (well, it is not really an issue, but it can be troublesome to deal with).

Exit Codes:
0 = Successful Run
1 = Successful Child Run
2 = Cannot parse config file
3 = Missing requirements
4 = Error with forking

Exception Codes
100 = InvalidClassException
200 = ExpiredTokenException
300 = DataNotFoundException

Token:
- hash(IP Address:Useragent)

Authentication:
- Request
-- username
-- token
-- passcode
--- hash(username:passcode)

Account Permission
- admin
- manage groups
- manage users
- manage events
- manage places
- enabled

Account Visibility
- global
- group
- event

Account Options
- enable 2-step
- email group invite
- email group new member
- email group event creation
- email group event end
- email event modification
- email private message
- email new place added

- allow global pm
- allow group pm
- allow event pm
