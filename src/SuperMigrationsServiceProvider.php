<?php

namespace ProAI\SuperMigrations;

use Illuminate\Support\ServiceProvider;
use ProAI\SuperMigrations\Console\MigrationCreator;
use ProAI\SuperMigrations\Console\MigrateMakeAltCommand;

class SuperMigrationsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCreator();

        $this->registerCommands();
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator.alt', function ($app) {
            return new MigrationCreator($app['files']);
        });
    }

    /**
     * Register all of the migration commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $commands = ['MakeAlt'];

        foreach ($commands as $command) {
            $this->{'register'.$command.'Command'}();
        }

        $this->commands(
            'command.migrate.make.alt'
        );
    }

    /**
     * Register the "make" migration alternative command.
     *
     * @return void
     */
    protected function registerMakeAltCommand()
    {
        $this->app->singleton('command.migrate.make.alt', function ($app) {
            $creator = $app['migration.creator.alt'];

            $composer = $app['composer'];

            return new MigrateMakeAltCommand($creator, $composer);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'migration.creator.alt',
            'command.migrate.make.alt'
        ];
    }
}
