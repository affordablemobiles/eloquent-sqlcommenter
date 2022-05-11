<?php

declare(strict_types=1);

namespace A1comms\sqlcommenter\PDO;

use Illuminate\Database\PDO\Connection as LaravelConnection;
use OpenCensus\Trace\Propagator\TraceContextFormatter;
use OpenCensus\Trace\Tracer;

class Connection extends LaravelConnection
{
    /**
     * Execute an SQL statement.
     */
    public function exec(string $statement): int
    {
        $statement = $this->appendSQLComment($statement);

        return parent::exec($statement);
    }

    /**
     * Prepare a new SQL statement.
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function prepare(string $sql): StatementInterface
    {
        $sql = $this->appendSQLComment($sql);

        return parent::prepare($sql);
    }

    /**
     * Execute a new query against the connection.
     *
     * @return \Doctrine\DBAL\Driver\Result
     */
    public function query(string $sql): ResultInterface
    {
        $sql = $this->appendSQLComment($sql);

        return parent::query($sql);
    }

    protected function appendSQLComment(string $sql): string
    {
        $trace_data = $this->getTraceData();

        if (empty($trace_data)) {
            return $sql;
        }

        $trace_data = $this->cleanTraceData($trace_data);

        return $sql.' /*'.implode('', $trace_data).'*/';
    }

    protected function getTraceData(): array
    {
        return [
            'framework'   => app()->version(),
            'route'       => request()->route()->getName() ?? request->path(),
            'controller'  => request()->route()->getActionName(),
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
