<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\PDO;

use A1comms\sqlcommenter\PDO\Concerns\ConnectsToDatabase;
use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;

class PostgresDriver extends AbstractPostgreSQLDriver
{
    use ConnectsToDatabase;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_pgsql';
    }
}
