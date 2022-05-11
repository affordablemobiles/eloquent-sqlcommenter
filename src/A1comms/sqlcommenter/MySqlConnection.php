<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter;

use A1comms\sqlcommenter\PDO\MySqlDriver;
use Illuminate\Database\MySqlConnection as LaravelMySqlConnection;

class MySqlConnection extends LaravelMySqlConnection
{
    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Illuminate\Database\PDO\MySqlDriver
     */
    protected function getDoctrineDriver()
    {
        return new MySqlDriver();
    }
}
