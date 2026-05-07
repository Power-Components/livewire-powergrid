@foreach ($actions as $action)
    @if (!empty($action['replaceHtml']))
        {!! $action['replaceHtml'] !!}
    @else
        @php
            $pgTag = $action['tag'] ?? 'button';
            $pgAttrsStr = collect($action['attributes'] ?? [])
                ->map(fn ($v, $k) => $k . '="' . e($v) . '"')
                ->implode(' ');
        @endphp
        {!! "<{$pgTag} {$pgAttrsStr}>" !!}
            @if (!empty($action['iconHtml'])){!! $action['iconHtml'] !!}@endif
            @if (!empty($action['slot'])){!! $action['slot'] !!}@endif
        {!! "</{$pgTag}>" !!}
    @endif
@endforeach
