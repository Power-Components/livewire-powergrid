<script>
    window.onload = function () {
        if (!window.Alpine) {
            console.warn('Oops. Could not find Alpine. Are you sure you installed it? See: https://alpinejs.dev/', {
                alpine: 'https://alpinejs.dev/',
                powergrid: 'https://github.com/Power-Components/livewire-powergrid',
            })
        } else {
            if (window.Alpine.version && /^2\..+\..+$/.test(window.Alpine.version)) {
                console.warn('Oops. Powergrid does not support V2.x', {
                    alpine: 'https://alpinejs.dev/',
                    powergrid: 'https://github.com/Power-Components/livewire-powergrid',
                })
            }
        }
    }
    
    livewire.hook('message.processed', (message, component) => {
        const multi_selects = $("div[wire\\:id='"+component.id+"']").find("select[x-ref^='select_picker_']");
        multi_selects.map(function () {
            let field_id = $(this).attr('x-ref').replace('select_picker_','');
            if ('multi_select' in message.response.serverMemo.data.filters) {
                if (field_id in message.response.serverMemo.data.filters.multi_select) {
                    $(this).selectpicker('val', message.response.serverMemo.data.filters.multi_select[field_id].values);
                } else {
                    $(this).selectpicker('val', []);
                }
            }
        });
    });
</script>

@if(filled(config('livewire-powergrid.plugins.flat_piker.js')))
    <script src="{{ config('livewire-powergrid.plugins.flat_piker.js') }}"></script>
@endif

@if(filled(config('livewire-powergrid.plugins.flat_piker.translate')))
    <script src="{{ config('livewire-powergrid.plugins.flat_piker.translate') }}"></script>
@endif

@if(isBootstrap5() && filled(config('livewire-powergrid.plugins.bootstrap-select.js')))
    <script src="{{ config('livewire-powergrid.plugins.bootstrap-select.js') }}" crossorigin="anonymous"></script>
@endif

@foreach(config('livewire-powergrid.alpinejs_cdn_plugins') as $plugin)
    <script src="{{ $plugin }}" defer></script>
@endforeach

@if(filled(config('livewire-powergrid.alpinejs_cdn')))
    <script src="{{ config('livewire-powergrid.alpinejs_cdn') }}" defer></script>
@endif

@isset($jsPath)
    <script>{!! file_get_contents($jsPath) !!}</script>
@endisset
