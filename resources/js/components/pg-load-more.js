export default () => ({
    init() {
        Livewire.on('pg:scrollTop', (event) => {
            setTimeout(() => {
                document.querySelector('body')
                    .scrollIntoView({ behavior: 'auto' })
            }, 0)
        })

        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                this.$wire.call('loadMore')
            }
        });

        observer.observe(this.$el)
    }
})
