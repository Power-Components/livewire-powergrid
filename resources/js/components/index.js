import pgToggleable from './pg-toggleable'
import pgFlatpickr from "./pg-flatpickr";
import pgEditable from "./pg-editable";
import pgCopyToClipboard from "./pg-copy-to-clipboard";

window.pgToggleable = pgToggleable
window.pgFlatpickr = pgFlatpickr
window.pgEditable = pgEditable

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pgToggleable', pgToggleable)
    window.Alpine.data('pgFlatpickr', pgFlatpickr)
    window.Alpine.data('phEditable', pgEditable)
    window.Alpine.plugin(pgCopyToClipboard)
})

import './select'
import './directives'
