window.pgAlpine.data('pgResponsive', () => ({
    expanded: null,
    hasHiddenElements: false,
    size: 0,
    resizeObserver: null,

    toggleExpanded(index) {
        this.expanded = this.expanded == index ? null : index
    },

    isExpanded(rowId) {
        return this.hasHiddenElements && this.expanded == rowId
    },

    rotateClass(rowId) {
        return this.expanded == rowId ? 'rotate-90' : 'rotate-0'
    },

    init() {
        this.onLivewireFinished = () => {
            setTimeout(() => this.handleResize(), 5)
        }

        this.$nextTick(() => {
            this.handleResize()

            this.observeElement()

            window.addEventListener('pg-livewire-request-finished', this.onLivewireFinished)
        })
    },

    destroy() {
        if (this.onLivewireFinished) {
            window.removeEventListener('pg-livewire-request-finished', this.onLivewireFinished)
        }
        this.resizeObserver?.disconnect()
    },

    handleResize() {
        const element = this.$el

        if (typeof window.pgOnResize === 'function') {
            window.pgOnResize(element)
        }

        this.hasHiddenElements = !!element.querySelector('table tr[expand] td div')?.innerHTML

        if (!this.hasHiddenElements) this.expanded = null
    },

    observeElement() {
        this.resizeObserver = new ResizeObserver(entries => {
            entries.forEach(entry => {
                if (entry.contentRect.width > 0) {
                    if (this.size === this.$el.getBoundingClientRect().width) {
                        return
                    }

                    this.size = this.$el.getBoundingClientRect().width

                    this.handleResize()
                }
            })
        })

        this.resizeObserver.observe(this.$el)
    }
}))

if (!window.pgResponsiveCommitHook) {
    window.pgResponsiveCommitHook = true

    document.addEventListener('DOMContentLoaded', () => {
        if (!window.Livewire) {
            return
        }

        Livewire.hook('commit', ({ component, succeed, fail }) => {
            if (component.ephemeral.setUp && component.ephemeral.setUp.hasOwnProperty('responsive')) {
                succeed(() => {
                    queueMicrotask(() => {
                        window.dispatchEvent(
                            new CustomEvent('pg-livewire-request-finished')
                        )
                    })
                })

                fail(() => {
                    window.dispatchEvent(
                        new CustomEvent('pg-livewire-request-finished')
                    )
                })
            }
        })
    })
}
