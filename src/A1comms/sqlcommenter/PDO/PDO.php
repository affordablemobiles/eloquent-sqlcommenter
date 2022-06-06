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

        return $sql.' /*'.implode(',', $trace_data).'*/';
    }

    protected function getTraceData(): array
    {
        return [
            'framework'   => 'Laravel '.app()->version(),
            'route'       => app()->runningInConsole() ? Arr::get(request()->server(), 'argv.1') : (request()->route()->getName() ?? request->path()),
            'controller'  => app()->runningInConsole() ? 'artisan' : request()->route()->getActionName(),
            'traceparent' => (new TraceContextFormatter())->serialize(
                Tracer::spanContext()
            ),
        ];
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
