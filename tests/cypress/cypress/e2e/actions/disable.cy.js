describe('can render actions buttons with icons', () => {
    beforeEach(() => {
        cy.visit('/actions-disable');
    });

    it('shoul be able to render "can" properly', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('contain.text', 'Disable')
            .should('not.have.attr', 'disabled')

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('contain.html', '<button disabled="disabled" class="text-slate-500 items-center flex gap-2 hover:text-slate-700 hover:bg-slate-100 p-1 px-2 rounded" data-cy="btn-disable-2">Disable</button>')
    });
})
