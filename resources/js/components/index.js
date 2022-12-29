import pgToggleable from './pg-toggleable'
import pgFlatPickr from "./pg-flat-pickr";
import pgEditable from "./pg-editable";
import pgCopyToClipboard from "./pg-copy-to-clipboard";

window.pgToggleable = pgToggleable
window.pgFlatPickr = pgFlatPickr
window.pgEditable = pgEditable

document.addEventListener('alpine:init', () => {
    window.Alpine.data('pgToggleable', pgToggleable)
    window.Alpine.data('pgFlatPickr', pgFlatPickr)
    window.Alpine.data('phEditable', pgEditable)
    window.Alpine.plugin(pgCopyToClipboard)
})

import './select'
