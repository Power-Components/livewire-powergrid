@inject('componentAttributeBag','\Illuminate\View\ComponentAttributeBag')
@props([
    'theme' => '',
    'input' => null,
])
@php
    $attributes = new $componentAttributeBag($input['attributes']);
@endphp
<div>
    <x-dynamic-component :component="$input['component']" :attributes="$attributes" />
</div>
