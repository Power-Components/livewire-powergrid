describe('render action rules buttons hide', () => {
    beforeEach(() => {
        cy.visit('/action-rules-button-hide');
    });

    it('can render buttons hide', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('not.contain.text', 'Edit-1')
            .should('contain.text', 'Delete-1')

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('not.contain.text', 'Delete-2')
            .should('contain.text', 'Edit-2')

        cy.get('.power-grid-table tbody tr').eq(2)
            .should('contain.text', 'Edit-3')
            .should('contain.text', 'Delete-3')

        cy.get('.power-grid-table tbody tr').eq(3)
            .should('contain.text', 'Edit-4')
            .should('contain.text', 'Delete-4')

        cy.get('.power-grid-table tbody tr').eq(4)
            .should('contain.text', 'Edit-5')
            .should('contain.text', 'Delete-5')
    });
})
