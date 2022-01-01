import pgMultiSelect from './pg-multi-select'
import pgToggleable from './pg-toggleable'
import pgMultiSelectBs5 from './pg-multi-select-bs5'
import pgFlatPickr from "./pg-flat-pickr";
import pgEditable from "./pg-editable";
import pgCopyToClipboard from "./pg-copy-to-clipboard";

window.pgMultiSelect = pgMultiSelect
window.pgToggleable = pgToggleable
window.pgMultiSelectBs5 = pgMultiSelectBs5
window.pgFlatPickr = pgFlatPickr
window.pgEditable = pgEditable

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pgMultiSelect', pgMultiSelect)
    window.Alpine.data('pgToggleable', pgToggleable)
    window.Alpine.data('pgMultiSelectBs5', pgMultiSelectBs5)
    window.Alpine.data('pgFlatPickr', pgFlatPickr)
    window.Alpine.data('phEditable', pgEditable)
    window.Alpine.plugin(pgCopyToClipboard)
})
