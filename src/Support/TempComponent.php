<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use PowerComponents\LivewirePowerGrid\Actions\Stubs;

final class TempComponent
{
    private string $componentName;

    private string $path = '';

    private string $stub = '';

    private ?Collection $stubVars;

    private string $datasource = '';

    private string $databaseTablename = '';

    private string $model = '';

    private string $modelFqn = '';

    public function __construct(string $componentName)
    {
        $this->componentName = $componentName;

        $livewirePath = powergrid_components_path();

        $path = powergrid_components_path($this->componentName . '.php');

        $this->componentFilename = Str::of($path)->basename();
        $basePath                = Str::of($path)->replace($this->componentFilename, '');

        $this->savedAt = $livewirePath . $basePath->after($livewirePath);


        $namespace = collect(explode('.', str_replace(['/', '\\'], '.', $this->componentName)))
                   ->map([Str::class, 'kebab'])
                   ->implode('.');


        $this->componentName = Str::of($namespace)
            ->lower()
            ->replace('/', '-')
            ->replace('\\', '-')
            ->prepend('<livewire:')
            ->append('/>');


        $subFolder = ''; //@TODO preciso dar algum tipo the before last '/'...





        $this->setVar('componentName', strval(config('livewire.class_namespace')));
        $this->setVar('livewireClassNamespace', strval(config('livewire.class_namespace')));
        $this->setVar('subFolder', '@TODO PEGAR ISSO');

    }

    public static function make(string $componentName): self
    {
        return new self($componentName);
    }

    public function setModelFqn(string $model, string $modelFqn): self
    {
        $this->model = $model;

        $this->modelFqn = $modelFqn;

        $this->setVar('modelName', $this->fqn);
        $this->setVar('modelLastName', $this->model);
        $this->setVar('modelLowerCase', str($this->model)->toLower()->toString());
        $this->setVar('modelKebabCase', str($this->model)->kebab()->toString());

        return $this;
    }

    public function setVar(string $var, string $value): self
    {
        if (is_null($this->stubVars)) {
            $this->stubVars = collect([]);
        }

        $this->stubVars->push([$var => $value]);

        return $this;
    }

    public function setDatasource(string $datasource): self
    {
        $this->datasource = $datasource;

        return $this;
    }

    public function loadStub(string $template = null): self
    {
        $this->stub = Stubs::load($this->datasource, $template);

        return $this;
    }

    public function setDatabaseTablename(string $databaseTablename): self
    {
        $this->databaseTablename = $databaseTablename;

        $this->setVar('databaseTableName', $this->databaseTablename);

        return $this;
    }

    public function process(): self
    {
        $stub = $this->stub;

        $this->stubVars->each(function (string $value, string $var) use (&$stub) {
            $stub = str_replace($var, $value, $stub);
        });

        $this->stub = $stub;

        return $this;
    }

    public function saveToDisk(): self
    {
        $this->process();

        File::ensureDirectoryExists($basePath);

        File::put($path, $this->stub);

        return $this;
    }

    /**
     * __get() magic method for properties formerly returned by current_theme_info()
     *
     * @return string Property value.
     */
    public function __get(string $name): string
    {
        if (property_exists($this, $name)) {
            return (string) $this->$name;
        }

        throw new \Exception("Attribute [{$name}] does not exist.");
    }
}
