document.addEventListener('alpine:init', () => {
    window.Alpine.directive('shift', (el, { expression }) => {
        el.addEventListener('click', (event) => {
            window.Livewire.find(expression).set('multiSortEnabled', event.shiftKey)
        })
    })
})
