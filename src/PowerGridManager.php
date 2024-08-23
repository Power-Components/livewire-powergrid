<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\Components\SetUp\{Cache,
    Detail,
    Exportable,
    Footer,
    Header,
    Lazy,
    Responsive
};

class PowerGridManager
{
    public function fields(): PowerGridFields
    {
        return app(PowerGridFields::class);
    }

    public function header(): Header
    {
        return app(Header::class);
    }

    public function footer(): Footer
    {
        return app(Footer::class);
    }

    public function detail(): Detail
    {
        return app(Detail::class);
    }

    public function responsive(): Responsive
    {
        return app(Responsive::class);
    }

    public function lazy(): Lazy
    {
        return app(Lazy::class);
    }

    public function cache(): Cache
    {
        return app(Cache::class);
    }

    public function exportable(string $fileName = 'export'): Exportable
    {
        return app(Exportable::class, [
            'fileName' => $fileName,
        ]);
    }
}
