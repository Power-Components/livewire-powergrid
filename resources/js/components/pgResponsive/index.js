import onResize from './onResize';

export default () => ({
    running: false,
    expanded: null,
    element: null,
    hasHiddenElements: false,
    size: 0,

    toggleExpanded(index) {
        this.expanded = this.expanded == index ? null : index
    },

    isExpanded(rowId) {
        return this.hasHiddenElements && this.expanded == rowId
    },

    rotateClass(rowId) {
        return this.expanded == rowId ? 'rotate-90' : '-rotate-0'
    },

    init() {
        this.$nextTick(() => {
            this.handleResize()

            this.observeElement()

            window.addEventListener('pg-livewire-request-finished', () => {
                setTimeout(() => this.handleResize(), 5);
            })
        })
    },

    handleResize() {
        const element = this.$el

        onResize(element)

        this.hasHiddenElements = !!element.querySelector('table tr[expand] td div')?.innerHTML

        if (!this.hasHiddenElements) this.expanded = null
    },

    observeElement() {
        const resizeObserver = new ResizeObserver(entries => {
            entries.forEach(entry => {
                if (entry.contentRect.width > 0) {
                    if (this.size === this.$el.getBoundingClientRect().width) {
                        return;
                    }

                    this.size = this.$el.getBoundingClientRect().width

                    this.handleResize()
                }
            });
        });

        resizeObserver.observe(this.$el);
    }
})
