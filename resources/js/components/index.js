import pgToggleable from './pg-toggleable'
import pgFlatpickr from "./pg-flatpickr";
import pgEditable from "./pg-editable";
import pgResponsive from './pgResponsive';
import pgTomSelect from "./select/tomSelect";
import pgSlimSelect from "./select/slimSelect";
import pgLoadMore from "./pg-load-more";

window.pgToggleable = pgToggleable
window.pgFlatpickr = pgFlatpickr
window.pgEditable = pgEditable
window.pgResponsive = pgResponsive
window.pgTomSelect = pgTomSelect
window.pgSlimSelect = pgSlimSelect
window.pgLoadMore = pgLoadMore

Livewire.hook('commit', ({ component, succeed, fail }) => {
    if (component.ephemeral.setUp && component.ephemeral.setUp.hasOwnProperty('responsive')) {
        succeed(() => {
            queueMicrotask(() => {
                window.dispatchEvent(
                    new CustomEvent('pg-livewire-request-finished')
                );
            })
        })

        fail(() => {
            window.dispatchEvent(
                new CustomEvent('pg-livewire-request-finished')
            );
        })
    }
})
