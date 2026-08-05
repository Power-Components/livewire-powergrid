import pgDropdown from './pg-dropdown'

export default () => ({
    ...pgDropdown(),

    countChecked: [],

    init() {
        this.countChecked = this.$wire.entangle('checkboxValues')
    },

    isEmpty() {
        return this.countChecked.length === 0
    },

    disabledClass() {
        return this.isEmpty() ? 'cursor-not-allowed' : ''
    },

    countLabel() {
        return '(' + this.countChecked.length + ')'
    },
})
