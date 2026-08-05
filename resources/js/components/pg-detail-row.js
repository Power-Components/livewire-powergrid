export default () => ({
    collapsed: false,
    singleExpand: false,
    tableName: '',
    rowId: '',

    init() {
        this.collapsed = this.$wire.entangle('show')
        this.singleExpand = this.$wire.entangle('singleExpand')
        this.tableName = this.$el.dataset.tableName
        this.rowId = this.$el.dataset.rowId
    },

    visible() {
        return this.collapsed || (this.collapsed && this.singleExpand && this.$store.pgDetail.expanded[this.tableName] == this.rowId)
    },
})
