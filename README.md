## Initialization
- Just run `make install`
- You can also get help by all cmds, type `make help`.
- For creating migration: `make migrate-gen`
- For rollup migration: `make migrate`
- To start all services `make up`
- For any trouble use `make ccs` to clear cache

Before you begin edit .env file and put your `NGINX_HOST_HTTP_PORT`

## Description
Php 8.2. Native JS with minimum code is used. The database is *sqllite*. It turned out to be a miniature framework. Navigation construction is based on a routing file, there is a division of elements by access. Users are authorized
using data from the database. In JS code used different approach in different places. The working environment is based on *docker*.
The main control is done by commands from *makefile*. There are TODOs in the code, you can follow the recommendations on them.

## Task
Create a task book application (ToDo list).
PHP frameworks cannot be used, libraries can be used. Complex architecture is not needed.
You need to implement the MVC model in the application using pure PHP.

## Links
* [Phinx SCM](https://book.cakephp.org/phinx/0/en/migrations.html) for DB migrations
* [Medoo DB framework](https://medoo.in/)
* [Twig](https://twig.symfony.com/)

## TODO`s
- [X] add xdebug
- [X] create base route controller
- [X] add renderer and home template with assets and bootstrap
- [X] add some image in assets just for fun
- [X] realize migrations package and base migration
- [X] add DB service
- [X] create task model
- [X] show tasks list
- [X] add pagination feature in tasks list
- [X] add sort feature in tasks list, should be remembered in url path
- [X] add creating task form, success form and error
- [X] add insert data controller, input data validator
- [X] create user model
- [X] add auth page and auth controller
- [X] add session handler
- [X] add login (with logout)
- [X] edit task by admin
- [X] fill readme file
- [ ] write tests
- [ ] deploy into production
