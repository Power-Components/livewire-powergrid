<?php
use Illuminate\Support\Facades\{File, View};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

it('prepares action resources correctly', function () {
    $paths          = ['default' => 'resources/views/components/icons'];
    $allowed        = [];
    $iconAttributes = ['class' => 'w-5 text-red-600'];

    config()->set('livewire-powergrid.icon_resources.paths', $paths);
    config()->set('livewire-powergrid.icon_resources.allowed', $allowed);
    config()->set('livewire-powergrid.icon_resources.attributes', $iconAttributes);

    $files = [
        new SplFileInfo(base_path('resources/views/components/icons/icon1.blade.php')),
        new SplFileInfo(base_path('resources/views/components/icons/icon2.blade.php')),
    ];

    File::shouldReceive('allFiles')
        ->once()
        ->with(base_path('resources/views/components/icons'))
        ->andReturn($files);

    View::shouldReceive('file')
        ->twice()
        ->andReturnUsing(function ($path, $data) {
            return new class ($data) {
                private array $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function render(): string
                {
                    return '<svg ' . $this->data['attributes'] . ' xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>';
                }
            };
        });

    $component = new class () extends PowerGridComponent {
    };

    $expectedJson = json_encode([
        'default-icon1' => '<svg class="w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>',
        'default-icon2' => '<svg class="w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>',
    ]);

    expect($expectedJson)->toBe(\Livewire\invade($component)->prepareActionsResources());
});
