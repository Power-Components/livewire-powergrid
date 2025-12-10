<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use PowerComponents\LivewirePowerGrid\PowerGridFields;
use stdClass;

final class RowTransformer
{
    private array $fieldClosures;

    public function __construct(protected PowerGridFields $powerGridFields)
    {
        $this->fieldClosures = $this->powerGridFields->fields;
    }

    public function transform(object $row): stdClass
    {
        $transformed = new stdClass();

        foreach ($this->fieldClosures as $key => $closure) {
            $value = $closure($row);

            $transformed->{$key} = $value;
        }

        return $transformed;
    }
}
