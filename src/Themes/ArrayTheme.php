<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

/**
 * Data-first theme: define tokens as a plain nested array instead of the fluent
 * builder. Extend it and override struct() to return your tokens, or build one
 * ad-hoc with ArrayTheme::fromArray()/fromFile(). Everything not defined falls
 * back to the parent theme (Tailwind by default).
 *
 * Example (subclass):
 *
 *   class MyTheme extends ArrayTheme
 *   {
 *       protected ?string $parentTheme = Tailwind::class;
 *
 *       public function struct(): array
 *       {
 *           return ['footer' => ['pagination' => ['item' => 'btn ...']]];
 *       }
 *   }
 */
class ArrayTheme extends Theme
{
    /**
     * @param  array<string, mixed>  $definition
     */
    public function __construct(
        protected array $definition = [],
        ?string $parentTheme = Tailwind::class,
        protected ?string $themeName = null,
    ) {
        $this->parentTheme = $parentTheme;
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    public static function fromArray(array $definition, ?string $parentTheme = Tailwind::class, ?string $name = null): self
    {
        return new self($definition, $parentTheme, $name);
    }

    public static function fromFile(string $path, ?string $parentTheme = Tailwind::class, ?string $name = null): self
    {
        /** @var array<string, mixed> $definition */
        $definition = require $path;

        return new self($definition, $parentTheme, $name);
    }

    public function name(): string
    {
        return $this->themeName ?? parent::name();
    }

    /**
     * @return array<string, mixed>
     */
    public function struct(): array
    {
        return $this->definition;
    }
}
