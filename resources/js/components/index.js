import pgToggleable from './pg-toggleable'
import pgFlatpickr from "./pg-flatpickr";
import pgEditable from "./pg-editable";
import tableResponsive from './tableResponsive';
import pgTomSelect from "./select/tomSelect";
import pgSlimSelect from "./select/slimSelect";

window.pgToggleable = pgToggleable
window.pgFlatpickr = pgFlatpickr
window.pgEditable = pgEditable
window.tableResponsive = tableResponsive
window.pgTomSelect = pgTomSelect
window.pgSlimSelect = pgSlimSelect

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
