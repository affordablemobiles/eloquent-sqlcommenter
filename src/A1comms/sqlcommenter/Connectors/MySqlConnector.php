<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\Connectors;

use Illuminate\Database\Connectors\MySqlConnector as BaseMySqlConnector;

class MySqlConnector extends BaseMySqlConnector
{
    use Concerns\ExtendsPDO;
}
