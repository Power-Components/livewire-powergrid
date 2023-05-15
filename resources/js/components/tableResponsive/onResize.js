const EXPAND_ITEM_CONTAINER = ''
const SCROLLBAR_WIDTH = 12

function showAllItems(element) {
    element.querySelectorAll(`tbody tr td`).forEach((el) => { el.classList.remove('hidden') })
    element.querySelectorAll(`thead tr th`).forEach((el) => { el.classList.remove('hidden') })
}

function getTableUtilWidth(element) {
    let fixedSpace = 0;

    const fixedItems = element.querySelectorAll('table thead tr:nth-child(1) th[fixed]');

    fixedItems.forEach((element) => {
        fixedSpace += (element.clientWidth - 1)
    })

    return element.clientWidth - fixedSpace - SCROLLBAR_WIDTH;
}

function getItemsToHide(element, tableWidth) {
    const items = element.querySelectorAll('table thead tr:nth-child(1) th')

    let calc = 0;

    let fitsMoreITems = true;

    const itemsToHide = []

    for (const [index, item] of items.entries()) {
        if (item.getAttribute('fixed') !== null) {
            continue
        }

        const itemWidth = item.clientWidth - 1;

        if (fitsMoreITems && calc <= tableWidth && (calc + itemWidth <= tableWidth)) {
            calc += itemWidth;
        } else {
            itemsToHide.push(index + 1)
            fitsMoreITems = false
        }
    }

    return itemsToHide
}

function hideItems(element, items) {
    for (const item of items) {
        element.querySelectorAll(`tbody:not(tbody[expand]) tr td:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
        element.querySelectorAll(`thead tr th:nth-child(${item})`).forEach((el) => { el.classList.add('hidden') })
    }
}

function fillTableExpand(element, hideItems) {
    for (const expands of element.querySelectorAll('table tbody[expand] tr td div')) {
        expands.innerHTML = ""
    }

    if (!hideItems.length) return

    for (const hideItem of hideItems) {
        const rows = element.querySelectorAll('table tbody:not(tbody[expand])')

        for (const row of rows) {
            const expandContainer = row.nextElementSibling.querySelector('tr td div')

            const rowName = element.querySelector(`table thead tr th:nth-child(${hideItem})`).innerHTML

            const rowValue = row.querySelector(`tr:first-child td:nth-child(${hideItem})`).innerHTML

            if (!expandContainer.querySelector(`div[data-expand-item-${hideItem}]`)) {
                expandContainer.innerHTML += `<div data-expand-item-${hideItem}>
                    <span class="font-bold">${rowName}:</span>
                    <span>${rowValue}</span>
                </div>`
            }
        }
    }
}

export default function (element) {
    showAllItems(element)

    const tableUtilWidth = getTableUtilWidth(element)

    const itemsToHide = getItemsToHide(element, tableUtilWidth)

    // fillTableExpand(element, itemsToHide)

    hideItems(element, itemsToHide)
};
