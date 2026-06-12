<div {{ $attributes->merge(['class' => theme('layout.wrapper')]) }}>
    <div class="{{ theme('table.layout.container') }}">
        <table class="{{ theme('table.layout.table') }}">
            {{ $slot }}
        </table>
    </div>

    @if($paginate)
        <footer class="{{ theme('footer.layout.container') }} justify-end">
            <div class="min-w-0 w-full md:w-auto md:flex-1">
                {!! $paginate->links(theme_view('pagination'), [
                    'recordCount' => $recordCount,
                    'perPage' => $paginate->perPage(),
                ]) !!}
            </div>
        </footer>
    @endif
</div>
