import pgDropdown from './pg-dropdown'

export default () => ({
    ...pgDropdown(),

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
})
