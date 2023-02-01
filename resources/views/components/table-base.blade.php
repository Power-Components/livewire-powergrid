@props([
    'theme' => null,
    'readyToLoad' => false,
    'screenWidth' => null,
    'tableName' => null,
])
<div x-data="{ width: @entangle('screenWidth') }"
     x-init="() => {
        $nextTick(() => {
            width = $el.clientWidth;

            let timeout = null;
            window.addEventListener('resize', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => width = $el.clientWidth, 300)
            });
        })
     }"
     x-ref="{{ $tableName }}">
    <table class="table power-grid-table {{ $theme->tableClass }}"
           style="{{$theme->tableStyle}}">
        <thead class="{{$theme->theadClass}}"
               style="{{$theme->theadStyle}}">
                {{ $header }}
        </thead>
        @if($readyToLoad)
            <tbody class="{{$theme->tbodyClass}}"
                   style="{{$theme->tbodyStyle}}">
            {{ $rows }}
            </tbody>
        @else
            <tbody class="{{$theme->tbodyClass}}"
                   style="{{$theme->tbodyStyle}}">
            {{ $loading }}
            </tbody>
        @endif
    </table>
</div>
