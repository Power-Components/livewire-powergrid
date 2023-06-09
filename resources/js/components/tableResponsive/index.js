import onResize from './onResize';

export default () => ({
    running: false,
    expanded: null,
    element: null,
    hasHiddenElements: false,

    toggleExpanded(index) {
        this.expanded = this.expanded == index ? null : index
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

        this.hasHiddenElements = element.querySelector('table tbody[expand] tr td div')?.innerHTML

        if (!this.hasHiddenElements) this.expanded = null
    },

    observeElement() {
        const resizeObserver = new ResizeObserver(entries => {
            entries.forEach(entry => {
                if (entry.contentRect.width > 0) {
                   this.handleResize()
                }
            });
        });

        resizeObserver.observe(this.$el);
    }
})
