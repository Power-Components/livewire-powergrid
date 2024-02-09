<?php

namespace PowerComponents\LivewirePowerGrid\Events;

class MeasureRetrieveData
{
    /**
     * @param string $tableName Name of the table where the data was retrieved.
     * @param float $retrieveData Total time spent on the data retrieval operation.
     * @param float $queriesTime Total time spent on executing queries.
     * @param bool $cached Indicates whether the data was retrieved from the cache.
     * @param array $queries List of queries executed (query, binding, time).
     */
    public function __construct(
        public string $tableName,
        public float $retrieveData,
        public float $queriesTime = 0,
        public bool $cached = false,
        public array $queries = [],
    ) {
    }
}
