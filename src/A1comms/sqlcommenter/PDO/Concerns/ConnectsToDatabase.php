<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\PDO\Concerns;

use A1comms\sqlcommenter\PDO\Connection;
use InvalidArgumentException;
use PDO;

trait ConnectsToDatabase
{
    /**
     * Create a new database connection.
     *
     * @param mixed[]     $params
     * @param null|string $username
     * @param null|string $password
     * @param mixed[]     $driverOptions
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Database\PDO\Connection
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        if (!isset($params['pdo']) || !$params['pdo'] instanceof PDO) {
            throw new InvalidArgumentException('Laravel requires the "pdo" property to be set and be a PDO instance.');
        }

        return new Connection($params['pdo']);
    }
}
