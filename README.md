# Laravel Pro Migrations

[![Latest Stable Version](https://poser.pugx.org/proai/laravel-pro-migrations/v/stable)](https://packagist.org/packages/proailaravel-pro-migrations) [![Total Downloads](https://poser.pugx.org/proai/laravel-pro-migrations/downloads)](https://packagist.org/packages/proai/laravel-pro-migrations) [![Latest Unstable Version](https://poser.pugx.org/proai/laravel-pro-migrations/v/unstable)](https://packagist.org/packages/proai/laravel-pro-migrations) [![License](https://poser.pugx.org/proai/laravel-pro-migrations/license)](https://packagist.org/packages/proai/laravel-pro-migrations)

This is an extension for the Laravel migrations. It is useful if you have a big database that results in a lot of migration files. This package helps you to bundle migrations and to 

## Installation

Laravel Pro Migrations is distributed as a composer package. So you first have to add the package to your `composer.json` file:

```
"proai/laravel-pro-migrations": "~1.0@dev"
```

Then you have to run `composer update` to install the package.

## Usage

Firstly here is a migration file in the `database/migrations` folder. Notice that we extend the `ProAI\ProMigrations\Migration` class. Instead of an `up()` and a `down()` method this class needs a `schemas()` method:

```php
<?php

use ProAI\ProMigrations\Migration;

class InitProject extends Migration
{
    /**
     * Get class method names for up and down schemas.
     *
     * @return array
     */
    public function schemas()
    {
        return [
            'users' => 'create'
        ];
    }
}

```

The `schemas()` method returns an array with tablenames and a related name for the schema.

Hint: The class should be named after the update step and not any more after the table name. Also you can bundle migration steps within the `schemas()` method. Let's say we want to add a roles table and a column for the `role_id` in the users table, then the `schemas()` method could look like the following:

```php
return [
    'roles' => 'create',
    'users' => 'addRoleIdColumn'
];

```

For each tablename Laravel Pro Migrations searches for a file in `database\migrations\tables` with the same filename (i.e. for the table `users` there should exist a file `users.php`). This file must contain a class that extends `ProAI\ProMigrations\Table` and that is named after the table (in camel case) with a `Table` suffix. For example the classname must be `UsersTable` for a table `users`.

Do you remember that we specified names for each table for the schema in the migration class? For each of these names the table class must declare a method. Here is a sample table class that fits to the above migration class:

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use ProAI\ProMigrations\Table;

class UserTable extends Table
{
    /**
     * Create the table.
     *
     * @return void
     */
    public function create()
    {
        // migrations up
        $this->upSchema()->create(function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        // migrations down
        $this->downSchema()->dropIfExists();
    }
}

```

Within each schema method you can use `$this->upSchema()` and `$this->downSchema` to define the up and down schema. These methods return a `ProAI\ProMigrations\Builder` instance that is similar to the standard Laravel schema builder (see [Laravel docs](https://laravel.com/docs/5.3/migrations)). The only difference is that you don't need the tablename as first argument.

That's it! This way you have less migration files, because of bundling multiple steps. And you have a good overview over all existing tables and all modifications of a table in the mapped table class.

## Todo

- Add generator console commands

## Support

Bugs and feature requests are tracked on [GitHub](https://github.com/proai/laravel-pro-migrations/issues).

## License

This package is released under the [MIT License](LICENSE).

