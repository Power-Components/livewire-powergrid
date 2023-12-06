window.Alpine.directive('multisort-shift-click', (el, { expression }) => {
    el.addEventListener('click', (event) => {
        window.Livewire.find(expression).set('multiSort', event.shiftKey)
    })
})
