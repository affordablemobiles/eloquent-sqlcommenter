<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\PDO;

use Illuminate\Support\Arr;
use OpenCensus\Trace\Propagator\TraceContextFormatter;
use OpenCensus\Trace\Tracer;
use PDO as BasePDO;
use PDOStatement;

class PDO extends BasePDO
{
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        $query = $this->appendSQLComment($query);

        return parent::prepare($query, $options);
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {
        $query = $this->appendSQLComment($query);

        return parent::query($query, $fetchMode, ...$fetchModeArgs);
    }

    public function exec(string $statement): int|false
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

        $query=trim($query);

        if (';' === $query[-1]) {
            return rtrim($sql, ';').' /*'.implode(',', $trace_data).'*/'.';';
        }

        return $sql.' /*'.implode(',', $trace_data).'*/';
    }

    protected function getTraceData(): array
    {
        $comment = [
            'framework'   => 'laravel-'.app()->version(),
            'traceparent' => (new TraceContextFormatter())->serialize(
                Tracer::spanContext()
            ),
        ];

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
            if (!empty($action && $action['controller'] && str_contains($action['controller'], '@'))) {
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
            $return_data[] = urlencode($key).'='.$this->quote(urlencode($value));
        }

        sort($return_data);

        return $return_data;
    }
}
