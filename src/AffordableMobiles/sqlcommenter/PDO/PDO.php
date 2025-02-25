<?php

declare(strict_types=1);

namespace AffordableMobiles\sqlcommenter\PDO;

use Illuminate\Support\Arr;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use PDO as BasePDO;

class PDO extends BasePDO
{
    public function prepare(string $query, array $options = []): false|\PDOStatement
    {
        $query = $this->appendSQLComment($query);

        return parent::prepare($query, $options);
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): false|\PDOStatement
    {
        $query = $this->appendSQLComment($query);

        return parent::query($query, $fetchMode, ...$fetchModeArgs);
    }

    public function exec(string $statement): false|int
    {
        $statement = $this->appendSQLComment($statement);

        return parent::exec($statement);
    }

    protected function appendSQLComment(string $sql): string
    {
        $trace_data = $this->getTraceData();

        if (empty($trace_data)) {
            return $sql;
        }

        $trace_data = $this->cleanTraceData($trace_data);

        $sql=trim($sql);

        if (';' === $sql[-1]) {
            return rtrim($sql, ';').' /*'.implode(',', $trace_data).'*/;';
        }

        return $sql.' /*'.implode(',', $trace_data).'*/';
    }

    protected function getTraceData(): array
    {
        $comment = [
            'framework'   => 'laravel-'.app()->version(),
        ];

        TraceContextPropagator::getInstance()->inject($comment);
        unset($comment[TraceContextPropagator::TRACESTATE]);

        $action = null;

        if (app()->runningInConsole()) {
            $comment['controller'] = 'artisan';

            $comment['route'] = Arr::get(request()->server(), 'argv.1');
        } else {
            if (!empty(app('request')->route())) {
                $action = app('request')->route()->getAction();
            }

            if (!empty($action['controller'])) {
                $comment['controller'] = explode('@', class_basename($action['controller']))[0];
            }
            if (!empty($action) && !empty($action['controller']) && str_contains($action['controller'] ?? '', '@')) {
                $comment['action'] = explode('@', class_basename($action['controller']))[1];
            }

            $comment['route'] = request()->path();
        }

        $connection           = config('database.default');
        $comment['db_driver'] = config("database.connections.{$connection}.driver");

        return $comment;
    }

    protected function cleanTraceData(array $data): array
    {
        $return_data = [];

        foreach ($data as $key => $value) {
            $return_data[] = urlencode($key).'='.$this->quote($value ? urlencode($value) : '');
        }

        sort($return_data);

        return $return_data;
    }
}
