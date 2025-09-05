import pgToggleable from './pg-toggleable'
import pgFlatpickr from "./pg-flatpickr";
import pgEditable from "./pg-editable";
import pgResponsive from './pgResponsive';
import pgTomSelect from "./select/tomSelect";
import pgSlimSelect from "./select/slimSelect";
import pgLoadMore from "./pg-load-more";
import pgRenderActions from "./pg-render-actions";
import pgRenderRowTemplate from "./pg-render-row-template";
import pgRowAttributes from "./pg-row-attributes";

window.pgToggleable = pgToggleable
window.pgFlatpickr = pgFlatpickr
window.pgEditable = pgEditable
window.pgResponsive = pgResponsive
window.pgTomSelect = pgTomSelect
window.pgSlimSelect = pgSlimSelect
window.pgLoadMore = pgLoadMore
window.pgRenderActions = pgRenderActions
window.pgRowAttributes = pgRowAttributes
window.pgRenderRowTemplate = pgRenderRowTemplate

document.addEventListener("DOMContentLoaded", () => {
    Livewire.hook('commit', ({component, succeed, fail}) => {
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
})
