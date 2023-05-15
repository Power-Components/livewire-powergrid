import onResize from './onResize';

export default () => ({
    running: false,
    expanded: null,
    element: null,
    hasHiddenElements: false,

    toggleExpanded(index) {
        if (this.expanded == index) {
            this.expanded = null
        } else {
            this.expanded = index
        }

    },

    init() {
        // this.element = this.$el
        const element = this.$el

        this.$nextTick(() => {
            const resizeObserver = new ResizeObserver(entries => {
                entries.forEach(entry => {
                    if (entry.contentRect.width > 0) {

                        // this.hasHiddenElements = this.element.querySelector('table tbody[expand] tr td div')?.innerHTML

                        onResize(this.$el)
                    }
                });
            });

            resizeObserver.observe(this.$el);

            window.addEventListener('loading', function({ detail }) {
                const { loading } = detail

                if (loading) {
                    return;
                }

                onResize(element)
            })
        })
    }
})






// import Alpine from 'alpinejs';

// Alpine.directive('salada', (el, { modifiers, expression }, { Alpine, cleanup }) => {
//     const resizeObserver = new ResizeObserver(entries => {
//         entries.forEach(entry => {
//             if (entry.contentRect.width > 0) {
//                 onResize(el)
//             }
//         });
//     });

//     Alpine.nextTick(() => {
//         resizeObserver.observe(el);
//     })

//     window.addEventListener('loading', function({ detail }) {
//         const { loading } = detail

//         if (loading) {
//             return;
//         }

//         callback();
//     })

//     cleanup(() => {
//         resizeObserver.disconnect();
//     })
// })
