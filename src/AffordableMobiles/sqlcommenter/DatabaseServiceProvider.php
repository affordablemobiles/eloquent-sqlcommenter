<?php

declare(strict_types=1);

namespace AffordableMobiles\sqlcommenter;

use AffordableMobiles\sqlcommenter\Connectors\ConnectionFactory;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(): void {}

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', static fn ($app) => new ConnectionFactory($app));
    }
}
