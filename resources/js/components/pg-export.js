import pgDropdown from './pg-dropdown'

export default () => ({
    ...pgDropdown(),

    countChecked: [],

    init() {
        this.countChecked = this.$wire.entangle('checkboxValues')
    },

    count() {
        const checked = this.countChecked
        if (Array.isArray(checked)) return checked.length
        if (checked && typeof checked.length === 'number') return checked.length
        return 0
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
})
