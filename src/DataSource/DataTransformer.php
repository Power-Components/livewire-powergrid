<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\{Collection as BaseCollection, LazyCollection};
use PowerComponents\LivewirePowerGrid\{Contracts\PowerGridContext, ManageLoops};

final class DataTransformer
{
    private RowTransformer $rowTransformer;

    private ActionProcessor $actionProcessor;

    private string $primaryKey;

    public function __construct(protected PowerGridContext $component)
    {
        $this->rowTransformer = new RowTransformer($component->fields());
        $this->actionProcessor = new ActionProcessor($component);
        $this->primaryKey = $component->state()->primaryKey;
    }

    /** @param  BaseCollection<int, mixed>  $collection */
    public function transform(BaseCollection $collection): TransformResult
    {
        $startTime = microtime(true);
        $actionsByRow = [];

        $loopInstance = app(ManageLoops::class);
        $loopInstance->addLoop($collection);

        $collectActions = $this->component->shouldCollectActions();

        $transformedCollection = $collection->map(function ($row, $index) use ($loopInstance, &$actionsByRow, $collectActions) {
            $rowObject = (object) $row;

            $transformedData = $this->rowTransformer->transform($rowObject);

            $loopVars = $loopInstance->getLastLoop();

            $transformedData->__powergrid_loop = $loopVars;
            $transformedData->__powergrid_rules = $this->component->prepareActionRulesForRows($row, $loopVars);

            if ($collectActions) {
                $processedActions = $this->actionProcessor->process($rowObject);
                $transformedData->__powergrid_actions = $processedActions;

                $primaryKeyValue = data_get($row, $this->primaryKey);

                if ($primaryKeyValue && is_scalar($primaryKeyValue) && ! empty($processedActions)) {
                    $actionsByRow[(string) $primaryKeyValue] = $processedActions;
                }
            }

            if ($this->component->state()->supportModel && $row instanceof Model) {
                return (clone $row)->forceFill((array) $transformedData);
            }

            return $transformedData;
        });

        $endTime = round((microtime(true) - $startTime) * 1000);

        /** @var array<int|string, list<array<string, mixed>>> $actionsByRow */
        /** @var BaseCollection<int, mixed> $transformedCollection */
        return new TransformResult($transformedCollection, $endTime, $actionsByRow);
    }

    /**
     * Lazily transform rows for export: applies the same field closures as the
     * display path (and force-fills the model so both transformed fields and raw
     * attributes stay available), but one row at a time so a cursor-backed source
     * is never fully materialized in memory. Actions/rules/loop bookkeeping — not
     * used by the export writer — are skipped.
     *
     * @param  iterable<int, mixed>  $rows
     * @return LazyCollection<int, mixed>
     */
    public function transformForExport(iterable $rows): LazyCollection
    {
        return LazyCollection::make(function () use ($rows) {
            foreach ($rows as $row) {
                $transformedData = $this->rowTransformer->transform((object) $row);

                if ($this->component->state()->supportModel && $row instanceof Model) {
                    yield (clone $row)->forceFill((array) $transformedData);

                    continue;
                }

                yield $transformedData;
            }
        });
    }
}
