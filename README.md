# Laravel Super Migrations

[![Latest Stable Version](https://poser.pugx.org/proai/laravel-super-migrations/v/stable)](https://packagist.org/packages/proai/laravel-super-migrations) [![Total Downloads](https://poser.pugx.org/proai/laravel-super-migrations/downloads)](https://packagist.org/packages/proai/laravel-super-migrations) [![Latest Unstable Version](https://poser.pugx.org/proai/laravel-super-migrations/v/unstable)](https://packagist.org/packages/proai/laravel-super-migrations) [![License](https://poser.pugx.org/proai/laravel-super-migrations/license)](https://packagist.org/packages/proai/laravel-super-migrations)

This is an extension for the Laravel migrations. It is useful when you have a big database that results in a lot of migration files. This package will help you to reduce the number of migration files and furthermore it will give you a better structure with which you will have all schema updates for a table in one file.

## Installation

Laravel Super Migrations is distributed as a composer package. So you first have to add the package to your `composer.json` file:

```
"proai/laravel-super-migrations": "~1.0"
```

Then you have to run `composer update` to install the package.

Only if you want to use the `make:super-migration` command, you must add the service provider to the providers array in `config/app.php`:

```
'ProAI\SuperMigrations\SuperMigrationsServiceProvider'
```

## Usage

Basically we don't define table builder schemas by migration, but by table. For this purpose you need to create a `tables` folder in the `database/migrations` directory. For each table we will create a file in this new directory and link it in the migration files whereever needed. Here is a more detailed explanation:

### Migration Classes

Firstly here is a migration file in the `database/migrations` folder. Notice that we extend the `ProAI\SuperMigrations\Migration` class. Instead of an `up()` and a `down()` method this class needs a `schemas()` method:

```php
<?php

use ProAI\SuperMigrations\Migration;

class InitProject extends Migration
{
    /**
     * Get table names and related methods for up and down schemas.
     *
     * @return array
     */
    public function schemas()
    {
        return [
            'users' => 'create',
            'comments' => 'create'
        ];
    }
}

```

The `schemas()` method returns a list of all database tables that are affected by this migration (`users` and `comments` table in the example above). Since there can be more than one migration for a database table, we also need to assign a migration specific name for each table (i.e. `create` for the `users` and `comments` table).

With this pattern we can easily bundle schemas in one migration file. One more example: Let's say we want to add a roles table and a column for the `role_id` in the users table, then the `schemas()` method could look like the following:

```php
return [
    'roles' => 'create',
    'users' => 'addRoleIdColumn'
];

```

The idea behind this is that one migration file includes all schemas for a whole update step rather than just for a table (i.e. one migration `InitProject` vs. multiple migrations `CreateUsersTable`, `CreateCommentsTable` etc.). This way you will have less migration files.

### Table Classes

For each tablename that is returned by the `schemas()` method Laravel Super Migrations searches for a php file in `database/migrations/tables` with the same name (i.e. for the table `users` there must exist a file `users.php`). This file must contain a class that extends `ProAI\SuperMigrations\Table` and that is named after the table (in camel case) with a `Table` suffix. For example the classname must be `UsersTable` for a table `users`.

Furthermore for each of the migration specific names that we declared in the migration file, the table class must declare a method with the same name (i.e. a `create` method for the users table). Here is a sample users table class that fits to the migration class from the previous section:

```php
<?php

use Illuminate\Database\Schema\Blueprint;
use ProAI\SuperMigrations\Table;

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

We use `$this->upSchema()` and `$this->downSchema()` to define the up and down schema. These methods return a `ProAI\SuperMigrations\Builder` instance that is similar to the Laravel database schema builder (see [Laravel docs](https://laravel.com/docs/5.3/migrations)). The only difference is that you don't need the tablename as first argument, because the tablename is already known.

### Generator Command

Run `php artisan make:super-migration` to create a new migration class that fits to the pattern of this package. You can declare a custom path with the `--path` option. Note that you have to include the service provider in order to use this command (see installation section).

## Support

Bugs and feature requests are tracked on [GitHub](https://github.com/proai/laravel-super-migrations/issues).

## License

This package is released under the [MIT License](LICENSE).

