import pgToggleable from './pg-toggleable'
import pgFlatpickr from "./pg-flatpickr";
import pgEditable from "./pg-editable";
import pgCopyToClipboard from "./pg-copy-to-clipboard";
import pgResponsive from './pgResponsive';

window.pgToggleable = pgToggleable
window.pgFlatpickr = pgFlatpickr
window.pgEditable = pgEditable
window.pgResponsive = pgResponsive

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pgToggleable', pgToggleable)
    window.Alpine.data('pgFlatpickr', pgFlatpickr)
    window.Alpine.data('phEditable', pgEditable)
    window.Alpine.data('pgResponsive', pgResponsive)
    window.Alpine.plugin(pgCopyToClipboard)
})

import './select'
import './directives'
