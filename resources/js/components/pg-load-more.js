export default () => ({
    init() {
        Livewire.on('pg:scrollTop', (event) => {
            this.$nextTick(() => {
                document.getElementById('table_base_' + event.tableName)
                    .scrollIntoView({ behavior: 'smooth' })
            })
        })

        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                this.$wire.call('loadMore')
            }
        });

        observer.observe(this.$el)
    }
})
