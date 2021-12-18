import pgMultiSelect from './pg-multi-select'
import pgToggleable from './pg-toggleable'

import pgMultiSelectBs5 from './pg-multi-select-bs5'

window.pgMultiSelect = pgMultiSelect
window.pgToggleable = pgToggleable

window.pgMultiSelectBs5 = pgMultiSelectBs5

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pgMultiSelect', pgMultiSelect)
    window.Alpine.data('pgToggleable', pgToggleable)

    window.Alpine.data('pgMultiSelectBs5', pgMultiSelectBs5)
})

