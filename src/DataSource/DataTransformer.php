<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\{ManageLoops, PowerGridComponent};

final class DataTransformer
{
    private RowTransformer $rowTransformer;

    private ActionProcessor $actionProcessor;

    private string $primaryKey;

    public function __construct(protected PowerGridComponent $component)
    {
        $this->rowTransformer  = new RowTransformer($component->fields());
        $this->actionProcessor = new ActionProcessor($component);
        $this->primaryKey      = $component->primaryKey;
    }

    public function transform(BaseCollection $collection): TransformResult
    {
        $startTime    = microtime(true);
        $actionsByRow = [];

        $loopInstance = app(ManageLoops::class);
        $loopInstance->addLoop($collection);

        $transformedCollection = $collection->map(function ($row, $index) use ($loopInstance, &$actionsByRow) {
            $rowObject = (object) $row;

            $transformedData = $this->rowTransformer->transform($rowObject);

            $loopVars         = $loopInstance->getLastLoop();
            $processedActions = $this->actionProcessor->process($rowObject, $loopVars);

            $transformedData->__powergrid_loop    = $loopVars;
            $transformedData->__powergrid_actions = $processedActions['actions'];
            $transformedData->__powergrid_rules   = $processedActions['rules'];

            $loopInstance->incrementLoopIndices();

            $primaryKeyValue = data_get($row, $this->primaryKey);

            if ($primaryKeyValue && !empty($processedActions['actions'])) {
                $actionsByRow[$primaryKeyValue] = $processedActions['actions'];
            }

            if ($this->component->supportModel && $row instanceof Model) {
                return (clone $row)->forceFill((array) $transformedData);
            }

            return $transformedData;
        });

        $endTime = round((microtime(true) - $startTime) * 1000);

        return new TransformResult($transformedCollection, $endTime, $actionsByRow);
    }
}
