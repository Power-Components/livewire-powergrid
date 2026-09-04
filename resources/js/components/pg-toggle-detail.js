window.pgAlpine.data('pgToggleDetail', () => ({
    loading: false,
    tableName: '',
    rowId: '',

    init() {
        this.tableName = this.$el.dataset.tableName
        this.rowId = this.$el.dataset.rowId
    },

    isOpen() {
        return !!this.$wire.openedDetailIds[this.rowId]
    },

    async toggleDetail() {
        this.loading = true
        await this.$wire.toggleDetail(this.rowId)
        this.loading = false
    },

    notLoading() {
        return !this.loading
    },

    iconClass() {
        return {
            'rotate-90': this.isOpen(),
            'rotate-0': !this.isOpen(),
        }
    },
}))
