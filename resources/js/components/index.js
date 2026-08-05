import pgResponsive from './pgResponsive';
import pgTomSelect from "./select/tomSelect";
import pgSlimSelect from "./select/slimSelect";
import pgLoadMore from "./pg-load-more";
import pgRenderRowTemplate from "./pg-render-row-template";
import pgRowAttributes from "./pg-row-attributes";
import pgDropdown from "./pg-dropdown";
import pgExport from "./pg-export";
import pgToggleDetail from "./pg-toggle-detail";
import pgDetailRow from "./pg-detail-row";

window.pgResponsive = pgResponsive
window.pgTomSelect = pgTomSelect
window.pgSlimSelect = pgSlimSelect
window.pgLoadMore = pgLoadMore
window.pgRowAttributes = pgRowAttributes
window.pgRenderRowTemplate = pgRenderRowTemplate

document.addEventListener("alpine:init", () => {
    window.Alpine.data('pgResponsive', pgResponsive)
    window.Alpine.data('pgRowAttributes', pgRowAttributes)
    window.Alpine.data('pgRenderRowTemplate', pgRenderRowTemplate)
    window.Alpine.data('pgTomSelect', pgTomSelect)
    window.Alpine.data('pgSlimSelect', pgSlimSelect)
    window.Alpine.data('pgDropdown', pgDropdown)
    window.Alpine.data('pgExport', pgExport)
    window.Alpine.data('pgToggleDetail', pgToggleDetail)
    window.Alpine.data('pgDetailRow', pgDetailRow)
})

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
