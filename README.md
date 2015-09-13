# php-getting-started

This application supports the [Getting Started with PHP on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php) article - check it out.

## Deploying

Install the [Heroku Toolbelt](https://toolbelt.heroku.com/).

```sh
$ git clone https://github.com/freddiefujiwara/nikotama2015-backend.git
$ cd nikotama2015-backend
$ heroku create
$ git push heroku master
$ heroku addons:create heroku-postgresql
$ heroku open
```

## Documentation

For more information about using PHP on Heroku, see these Dev Center articles:

- [PHP on Heroku](https://devcenter.heroku.com/categories/php)
- [Postgres on Heroku](https://addons.heroku.com/heroku-postgresql)
