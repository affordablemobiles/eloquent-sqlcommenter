<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\Connectors;

use Illuminate\Database\Connectors\ConnectionFactory as LaravelConnectionFactory;
use Illuminate\Database\Connectors\SQLiteConnector;
use Illuminate\Database\Connectors\SqlServerConnector;
use InvalidArgumentException;

class ConnectionFactory extends LaravelConnectionFactory
{
    /**
     * Create a connector instance based on the configuration.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Database\Connectors\ConnectorInterface
     */
    public function createConnector(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }

        return match ($config['driver']) {
            'mysql'  => new MySqlConnector(),
            'pgsql'  => new PostgresConnector(),
            'sqlite' => new SQLiteConnector(),
            'sqlsrv' => new SqlServerConnector(),
            default  => throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]."),
        };
    }
}
