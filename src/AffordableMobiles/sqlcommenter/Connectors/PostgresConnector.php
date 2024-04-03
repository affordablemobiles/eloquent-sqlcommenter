<?php

declare(strict_types=1);

namespace AffordableMobiles\sqlcommenter\Connectors;

use Illuminate\Database\Connectors\PostgresConnector as BasePostgresConnector;

class PostgresConnector extends BasePostgresConnector
{
    use Concerns\ExtendsPDO;
}
