<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\PDO;

use A1comms\sqlcommenter\PDO\Concerns\ConnectsToDatabase;
use Doctrine\DBAL\Driver\AbstractMySQLDriver;

class MySqlDriver extends AbstractMySQLDriver
{
    use ConnectsToDatabase;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_mysql';
    }
}
