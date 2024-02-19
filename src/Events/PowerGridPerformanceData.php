<?php

namespace PowerComponents\LivewirePowerGrid\Events;

class PowerGridPerformanceData
{
    /**
     * @param string $tableName Name of the table where the data was retrieved.
     * @param float $retrieveDataInMs Total time spent on the data retrieval operation.
     * @param float $queriesTimeInMs Total time spent on executing queries.
     * @param bool $isCached
     * @param array $queries List of queries executed (query, binding, time).
     */
    public function __construct(
        public string $tableName,
        public float $retrieveDataInMs,
        public float $queriesTimeInMs = 0,
        public bool $isCached = false,
        public array $queries = [],
    ) {
    }
}
