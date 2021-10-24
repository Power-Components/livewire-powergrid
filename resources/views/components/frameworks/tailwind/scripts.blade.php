<script>
    function saveToggleableInput(value, id, field) {
        document.getElementsByClassName('message')[0].style.display = "none";
        window.livewire.emit('eventToggleChanged', {
            id: id,
            field: field,
            value: value
        })
    }

    function hiddenInvalidCol()
    {
        const table = document.querySelector('.power-grid-table');

        for (let i in table.rows) {
            let row = table.rows[i]
            for (let j in row.cells) {
                let col = row.cells[j]
                if (col.classList !== undefined) {
                    if (col.classList.length === 0) {
                        col.setAttribute('style', 'display:none')
                    }
                }
            }
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        hiddenInvalidCol()

        Livewire.hook('element.updated', (el, component) => {
            hiddenInvalidCol()
        })
    });
</script>
