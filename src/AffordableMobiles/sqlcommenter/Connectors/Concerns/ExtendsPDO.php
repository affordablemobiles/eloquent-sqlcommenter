<?php

declare(strict_types=1);

namespace AffordableMobiles\sqlcommenter\Connectors\Concerns;

use AffordableMobiles\sqlcommenter\PDO\PDO;

trait ExtendsPDO
{
    /**
     * Create a new PDO connection instance.
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     *
     * @return \PDO
     */
    protected function createPdoConnection($dsn, $username, $password, $options)
    {
        return new PDO($dsn, $username, $password, $options);
    }
}
