# CakePHP 5 

This repo is the result of loosely following the CMS Tutorial at CakePHP.org

It contains an example of using Token Authentication to authenticate Ajax requests

https://book.cakephp.org/5/en/tutorials-and-examples/cms/installation.html 

## Features
- Authentication and Authorization Plugins
- Migrations and Seeds to create posts and users table schema
- Policy files and use of the `BeforePolicyInterface` (see src/Policy/UserPolicy.php)
- Posts & Users Table (Not Articles and Tags tables)
- Example of POST and GET Ajax request using Token Authentication http://localhost:8765/posts/ajax using a token stored hashed in the database. 
- Async ajax `fetch` code in `webroot/js/ajax.js`
- View `src/Application.php` for example to bypass CSRF Middleware code for clients that send POST with no prior GET request
- Example of `CustomRedirectHandler` http://localhost:8765/posts/test-redirect

## Test Ajax Endpoint Using Curl
Use curl to test POST

```
curl -v -X POST \
    -H "Content-Type: application/json" \
    -H 'X-Requested-With: XMLHttpRequest' \
    -H 'Authorization: Token bbb' \
    -H 'Accept: application/json' \
    -H 'X-My-Custom-Header: hijames' \
    -d @content.json \
    http://localhost:8765/posts/ajax
```

## Passwords

user | pass | token
------ | ---------- | -----
test@example.com | 123 | aaaa
test1@example.com | 456 | bbbb

Change default passwords in `config/Seeds/UsersSeed.php` and then re-run `bin/cake migrations seed`


## Installation 
```
git clone https://github.com/toggenation/cakephp5-with-auth-plugins

cd cakephp5-with-auth-plugins/

composer install
```

```php
# Modify config/app_local.php to use Sqlite and have the 
# encrypted cookie middleware

  'Datasources' => [
        'default' => [
            'driver' => Sqlite::class,
            'database' => ROOT . DS . 'token',
        ],

# Add the settings for Encrypted Cookie Middleware
 'Security' => [
        'cookieKey' => '__SALT__',
        'encryptedCookies' => ['form'],
        //...
```

```sh
# run composer to replace the __SALT__ token

composer run post-install-cmd

bin/cake migrations migrate

# Warning seeds will truncate the posts and users table prior to inserting their records
bin/cake migrations seed

# Run the dev server
bin/cake server
```

