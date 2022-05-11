<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\Connectors;

use A1comms\sqlcommenter\MySqlConnection;
use A1comms\sqlcommenter\PostgresConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory as LaravelConnectionFactory;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Database\SqlServerConnection;
use InvalidArgumentException;

class ConnectionFactory extends LaravelConnectionFactory
{
    /**
     * Create a new connection instance.
     *
     * @param string        $driver
     * @param \Closure|\PDO $connection
     * @param string        $database
     * @param string        $prefix
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Database\Connection
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        return match ($driver) {
            'mysql'  => new MySqlConnection($connection, $database, $prefix, $config),
            'pgsql'  => new PostgresConnection($connection, $database, $prefix, $config),
            'sqlite' => new SQLiteConnection($connection, $database, $prefix, $config),
            'sqlsrv' => new SqlServerConnection($connection, $database, $prefix, $config),
            default  => throw new InvalidArgumentException("Unsupported driver [{$driver}]."),
        };
    }
}
