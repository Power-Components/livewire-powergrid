<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Support\{Collection, Str, Stringable};
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\DataSourceProcessorInterface;

class ScoutBuilderProcessor extends DataSourceBase implements DataSourceProcessorInterface
{
    public static function match(mixed $key): bool
    {
        return $key instanceof ScoutBuilder;
    }

    public function process(): Collection
    {
        /** @var ScoutBuilder $datasource */
        $datasource = $this->prepareDataSource();

        $datasource->query = Str::of($datasource->query)
            ->when($this->component->search != '', fn (Stringable $self) => $self
                ->prepend($this->component->search . ','))
            ->toString();

        collect($this->component->filters)->each(fn (array $filters) => collect($filters)
            ->each(fn (string $value, string $field) => $datasource
                ->where($field, $value)));

        if ($this->component->multiSort) {
            foreach ($this->component->sortArray as $sortField => $direction) {
                $datasource->orderBy($sortField, $direction);
            }
        } else {
            $datasource->orderBy($this->component->sortField, $this->component->sortDirection);
        }

        $results = self::applyPerPage($datasource);

        if (method_exists($results, 'total')) {
            $this->component->total = $results->total();
        }

        return $results->setCollection( // @phpstan-ignore-line
            $this->transform($results->getCollection(), $this->component) // @phpstan-ignore-line
        );
    }
}
