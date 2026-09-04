window.pgDropdownFactory = () => ({
    open: false,

    toggle() {
        this.open = !this.open
    },

    openMenu() {
        this.open = true
    },

    close() {
        this.open = false
    },

    activeClass() {
        return this.open ? (this.$el.dataset.openClass || '') : ''
    },

    dispatch(event, key, value) {
        this.$wire.dispatch(event, key ? { [key]: value } : {})
    },

    dispatchClose(event, key, value) {
        this.dispatch(event, key, value)
        this.close()
    },
})

window.pgAlpine.data('pgDropdown', window.pgDropdownFactory)
