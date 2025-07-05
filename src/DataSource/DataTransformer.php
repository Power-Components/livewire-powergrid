<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\{ManageLoops, PowerGridComponent};
use stdClass;

final class DataTransformer
{
    private RowTransformer $rowTransformer;

    private ActionProcessor $actionProcessor;

    public function __construct(protected PowerGridComponent $component)
    {
        $this->rowTransformer  = new RowTransformer($component->fields());
        $this->actionProcessor = new ActionProcessor($component);
    }

    public function transform(BaseCollection $collection): TransformResult
    {
        $startTime = microtime(true);

        $loopInstance = app(ManageLoops::class);
        $loopInstance->addLoop($collection);

        $transformedCollection = $collection->map(function ($row, $index) use ($loopInstance) {
            $rowObject = (object) $row;

            $transformedData = $this->rowTransformer->transform($rowObject);

            $loopVars         = $loopInstance->getLastLoop();
            $processedActions = $this->actionProcessor->process($rowObject, $loopVars);

            $transformedData->__powergrid_loop    = $loopVars;
            $transformedData->__powergrid_actions = $processedActions['actions'];
            $transformedData->__powergrid_rules   = $processedActions['rules'];

            $loopInstance->incrementLoopIndices();

            if ($this->component->supportModel && $row instanceof Model) {
                return (clone $row)->forceFill((array) $transformedData);
            }

            return $transformedData;
        });

        $endTime = round((microtime(true) - $startTime) * 1000);

        return new TransformResult($transformedCollection, $endTime);
    }
}
