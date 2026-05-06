<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

abstract class Theme
{
    protected array $tokens = [];

    protected ?string $parentTheme = Tailwind::class;

    public static function make(): static
    {
        return new static();
    }

    abstract public function struct(): array;

    public function name(): string
    {
        return strval(str(static::class)->afterLast('\\')->kebab());
    }

    public function views(): array
    {
        return [];
    }

    public function merge(array $overrides): static
    {
        $this->tokens = array_replace_recursive($this->resolveTokens(), $overrides);

        return $this;
    }

    public function resolveTokens(): array
    {
        if (empty($this->tokens)) {
            $tokens = $this->struct();

            if ($this->parentTheme && $this->parentTheme !== static::class) {
                $parentTokens = (new $this->parentTheme())->resolveTokens();
                $tokens = array_replace_recursive($parentTokens, $tokens);
            }

            // Merge filter(), editable(), toggleable() into tokens so theme() helper can resolve them
            foreach (['filter', 'editable', 'toggleable'] as $method) {
                $extra = $this->{$method}();
                if (! empty($extra)) {
                    $tokens = array_replace_recursive($tokens, $extra);
                }
            }

            $this->tokens = $tokens;
        }

        return $this->tokens;
    }

    public function resolveView(string $alias): string
    {
        $views = $this->views();

        if (isset($views[$alias])) {
            return $views[$alias];
        }

        $tokens = $this->resolveTokens();
        $aliasNormalized = str_replace('-', '_', $alias);

        // 1. token direto: 'header.view' or 'footer.pagination.view'
        $tokenView = data_get($tokens, $aliasNormalized);

        // 2. prefixo view_: 'header.export' → tokens['header']['view_export']
        if (empty($tokenView) || ! is_string($tokenView)) {
            $path = ! str_contains($aliasNormalized, '.')
                ? 'view_'.$aliasNormalized
                : str_replace('.', '.view_', $aliasNormalized);

            $tokenView = data_get($tokens, $path);
        }

        // 3. sufixo .view: 'header.export' → tokens['header']['export']['view']
        if (empty($tokenView) || ! is_string($tokenView)) {
            $tokenView = data_get($tokens, $aliasNormalized.'.view');
        }

        // 4. search_box alias especial
        if (empty($tokenView) && str_contains($aliasNormalized, 'search')) {
            $path = str_replace('search', 'search_box', $aliasNormalized).'.view';
            $tokenView = data_get($tokens, $path);
        }

        if (is_string($tokenView) && ! empty($tokenView)) {
            return $tokenView;
        }

        // 5. fallback por seção (header/table/footer)
        foreach (['header', 'table', 'footer'] as $prefix) {
            $tokenView = data_get($tokens, $prefix.'.view_'.$aliasNormalized)
                ?? data_get($tokens, $prefix.'.'.$aliasNormalized.'.view');

            if (is_string($tokenView) && ! empty($tokenView)) {
                return $tokenView;
            }
        }

        // 6. fallback automático via baseView + alias
        // Se nenhum token explícito foi encontrado, compõe a view a partir do
        // baseView + alias em dot notation. O tema só precisa declarar uma view
        // quando quer SOBRESCREVER o padrão.
        // Ex: baseView='...tailwind' + alias='header.export' → '...tailwind.header.export'
        $baseView = data_get($tokens, 'base_view');

        if ($baseView) {
            $candidate = rtrim($baseView, '.').'.'.str_replace('_', '-', $aliasNormalized);

            if (view()->exists($candidate)) {
                return $candidate;
            }
        }

        // 7. fallback para o tema pai
        if ($this->parentTheme && $this->parentTheme !== static::class) {
            return (new $this->parentTheme())->resolveView($alias);
        }

        return '';
    }

    public function get(string $key, string $default = ''): string
    {
        $value = data_get($this->resolveTokens(), $key, $default);

        if (is_array($value)) {
            return $default;
        }

        return strval($value);
    }

    // Estruturais — fazem parte do resolveTokens() via struct()
    // (checkbox e radio agora vivem dentro de table()->checkbox() e table()->radio())

    // Opcionais — chamados diretamente por quem precisa, fora do resolveTokens()
    public function editable(): array
    {
        return [];
    }

    public function filter(): array
    {
        return [];
    }

    public function toggleable(): array
    {
        return [];
    }
}
