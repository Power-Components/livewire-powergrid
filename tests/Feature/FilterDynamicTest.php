<?php

use function Pest\Livewire\livewire;

it(
    'properly filters using dynamic filter feature',
    fn (string $component, object $params) => livewire($component)
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            '<div>class: min-w-[170px]</div>',
            '<div>options: [{&quot;name&quot;:&quot;Active&quot;,&quot;value&quot;:true},{&quot;name&quot;:&quot;Inactive&quot;,&quot;value&quot;:false}]</div>',
            '<div>option-label: name</div>',
            '<div>option-value: value</div>',
            '<div>placeholder: Choose</div>',
        ])
)->with('themes with dynamic filter table');
