window.pgAlpine.data('pgExport', () => ({
    ...(typeof window.pgDropdownFactory === 'function' ? window.pgDropdownFactory() : {
        open: false,
        toggle() { this.open = !this.open },
        openMenu() { this.open = true },
        close() { this.open = false },
    }),

    count() {
        const checked = this.$wire.get('checkboxValues')
        return Array.isArray(checked) ? checked.length : 0
    },

    isEmpty() {
        return this.count() === 0
    },

    disabledClass() {
        return this.isEmpty() ? 'cursor-not-allowed' : ''
    },

    countLabel() {
        return '(' + this.count() + ')'
    },
}))
