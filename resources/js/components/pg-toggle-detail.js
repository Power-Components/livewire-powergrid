export default () => ({
    collapsed: false,
    loading: false,
    singleExpand: false,
    tableName: '',
    rowId: '',

    init() {
        this.singleExpand = this.$el.dataset.singleExpand === 'true'
        this.tableName = this.$el.dataset.tableName
        this.rowId = this.$el.dataset.rowId
    },

    toggleDetail() {
        const isOpen = this.collapsed

        if (this.singleExpand) {
            this.$dispatch('pg-toggle-detail-' + this.tableName + '-hidden-all')
            this.$store.pgDetail.expanded[this.tableName] = this.rowId
        }

        this.loading = true
        this.collapsed = !isOpen

        this.$dispatch('pg-toggle-detail-' + this.tableName + '-' + this.rowId, {
            collapsed: this.collapsed,
        })
    },

    resetCollapsed() {
        this.collapsed = false
    },

    stopLoading() {
        this.loading = false
    },

    notLoading() {
        return !this.loading
    },

    iconClass() {
        return {
            'rotate-90': this.collapsed && this.$store.pgDetail.expanded[this.tableName] == this.rowId,
            '-rotate-0': !this.collapsed,
        }
    },
})
