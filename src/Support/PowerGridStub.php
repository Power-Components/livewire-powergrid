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
        $this->content   = File::get($template);
        $this->variables = collect();
    }

    public static function make(string $template): self
    {
        return new self($template);
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
