document.addEventListener('alpine:init', () => {
    window.Alpine.directive('shift', (el, { expression }) => {
        el.addEventListener('click', (event) => {
            console.log(expression)
            window.Livewire.find(expression).set('multiSortEnabled', event.shiftKey)
        })
    })
})
