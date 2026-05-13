import pgResponsive from './pgResponsive';
import pgTomSelect from "./select/tomSelect";
import pgSlimSelect from "./select/slimSelect";
import pgLoadMore from "./pg-load-more";
import pgRenderRowTemplate from "./pg-render-row-template";
import pgRowAttributes from "./pg-row-attributes";

window.pgResponsive = pgResponsive
window.pgTomSelect = pgTomSelect
window.pgSlimSelect = pgSlimSelect
window.pgLoadMore = pgLoadMore
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
