# CakePHP 5 

This repo is the result of following the CMS Tutorial

https://book.cakephp.org/5/en/tutorials-and-examples/cms/installation.html 

Authentication and Authorization Plugins

Migrations and Seeds to create schema

Policy files and use of the `BeforePolicyInterface` (see src/Policy/UserPolicy.php)

Posts & Users Table (Not Articles and Tags tables)

Example of POST and GET Ajax request using Token Authentication http://localhost:8765/posts/ajax using a token stored hashed in the database. View the ajax code in `webroot/js/ajax.js`

Check `src/Application.php` for example to bypass CSRF Middleware code for clients that send POST with no prior GET request

Use curl to test POST

```
curl -v -X POST \
    -H "Content-Type: application/json" \
    -H 'X-Requested-With: XMLHttpRequest' \
    -H 'Authorization: Token ssss' \
    -H 'Accept: application/json' \
    -H 'X-My-Custom-Header: hijames' \
    -d @content.json \
    http://localhost:8765/posts/ajax
```


Example of `CustomRedirectHandler` http://localhost:8765/posts/test-redirect

user | pass
------ | ----------------
test@example.com | 123
test1@example.com | 456

Change default passwords in `config/Seeds/UsersSeed.php`

```
git clone [this repo]

composer install

bin/cake migrations migrate

# Warning seeds will truncate the posts and users table prior to inserting their records
bin/cake migrations seed

bin/cake server
```

