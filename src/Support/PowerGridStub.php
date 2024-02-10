<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

final class PowerGridStub
{
    /** @var Collection<string, string> $variables */
    private Collection $variables;

    private string $content = '';

    public function __construct(string $template)
    {
        $this->variables = collect();
        $this->content   = File::get($template);

        $this->ensureContentCompatibility();
    }

    public static function make(string $template): self
    {
        return new self($template);
    }

    private function ensureContentCompatibility(): void
    {
        // Ensure legacy variables are properly replaced.
        // The order variables are replaced interfere in the result
        $this->content = str($this->content)
             ->replace('{{ livewireClassNamespace }}', '{{ namespace }}')
             ->replace('{{ subFolder }}', '')
             ->replace('\{{ modelName }}', '{{ model }}')
             ->replace('{{ modelName }}', '{{ modelFqn }}')
             ->replace('{{ modelLastName }}', '{{ model }}')
             ->replace('{{ datasource }}', '{{ PowerGridFields }}')
             ->replace('{{ dataBaseTableName }}', '{{ databaseTableName }}')
             ->toString();
    }

    public function render(): string
    {
        $this->variables->each(function ($value, $var) {
            $this->content = str_replace("{{ {$var} }}", $value, $this->content);
        });

        return $this->content;
    }

    /**
     *
     * @return Collection<int, string>
     */
    public function listStubVars(): Collection
    {
        return str($this->content)->matchAll("/\{\{ ([^W]+?) \}\}/");
    }

    /**
     *
     * @return Collection<string, string>
     */
    public function listVars(): Collection
    {
        return $this->variables;
    }

    public function unsetVar(string $var): self
    {
        $this->variables->forget($var);

        return $this;
    }

    public function setVar(string $var, string $value): self
    {
        $this->variables->put($var, $value);

        return $this;
    }
}
