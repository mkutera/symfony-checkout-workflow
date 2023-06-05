<p align="center"><a href="https://symfony.com" target="_blank">
    <img src="https://symfony.com/logos/symfony_black_02.svg">
</a></p>

[Symfony][1] is a **PHP framework** for web and console applications and a set
of reusable **PHP components**. Symfony is used by thousands of web
applications and most of the [popular PHP projects][2].

Requirements
------------
* Install PHP 8.1 or higher and these PHP extensions: `Ctype`_, `iconv`_,
  `PCRE`_, `Session`_, `SimpleXML`_, and `Tokenizer`_; 
* Install Composer, which is used to install PHP packages.
* PDO-SQLite PHP extension enabled for default database configuration
* Install Symfony CLI for local web server


Installation
------------
1. Clone the repository
```bash
git clone https://github.com/mkutera/symfony-checkout-workflow.git && cd symfony-checkout-workflow
```
2. Install dependencies
```bash
composer install
```
3. Copy env file and set your database credentials. 
The SQLite database is used by default. (Make sure that the file app.db is writable and sqlite3 extension is enabled)
```bash
cp .env .env.local
```
4. Run migrations
```bash
php bin/console doctrine:migrations:migrate
```
5. Run fixtures
```bash
php bin/console doctrine:fixtures:load
```
6. Run the server
```bash
symfony server:start
```
7. Open the browser and go to generated url

[1]: https://symfony.com
[2]: https://symfony.com/projects
