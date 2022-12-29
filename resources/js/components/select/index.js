import pgTomSelect from "./tomSelect";
import pgSlimSelect from "./slimSelect";

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pgTomSelect', pgTomSelect)
    window.Alpine.data('pgSlimSelect', pgSlimSelect)
})
