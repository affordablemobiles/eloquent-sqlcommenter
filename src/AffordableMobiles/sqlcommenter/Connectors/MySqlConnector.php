<?php

declare(strict_types=1);

namespace AffordableMobiles\sqlcommenter\Connectors;

use Illuminate\Database\Connectors\MySqlConnector as BaseMySqlConnector;

class MySqlConnector extends BaseMySqlConnector
{
    use Concerns\ExtendsPDO;
}
