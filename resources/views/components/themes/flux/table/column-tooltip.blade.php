@props([
    'display' => '',
    'full' => '',
    'position' => 'top',
])

{{-- Flux tooltip: shows the full, untruncated value on hover/focus.
     $display is already escaped; $full is the raw value escaped by Flux. --}}
<flux:tooltip :content="$full" :position="$position">
    <span>{!! $display !!}</span>
</flux:tooltip>
