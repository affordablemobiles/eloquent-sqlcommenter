<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter;

use A1comms\sqlcommenter\PDO\PostgresDriver;
use Illuminate\Database\MySqlPostgresConnection as LaravelPostgresConnection;

class PostgresConnection extends LaravelPostgresConnection
{
    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Illuminate\Database\PDO\PostgresDriver
     */
    protected function getDoctrineDriver()
    {
        return new PostgresDriver();
    }
}
