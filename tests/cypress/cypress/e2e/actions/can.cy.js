describe('can render actions buttons with icons', () => {
    beforeEach(() => {
        cy.visit('/actions-can');
    });

    it('shoul be able to render "can" properly', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('contain.text', '*Cannot*')
            .should('not.contain.text', '*Can*')

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('contain.text', '*Can*')
            .should('not.contain.text', '*Cannot*')

        cy.get('.power-grid-table tbody tr').eq(2)
            .should('contain.text', '*Cannot*')
            .should('not.contain.text', '*Can*')

        cy.get('.power-grid-table tbody tr').eq(3)
            .should('contain.text', '*Cannot*')
            .should('not.contain.text', '*Can*')

        cy.get('.power-grid-table tbody tr').eq(4)
            .should('contain.text', '*Cannot*')
            .should('not.contain.text', '*Can*')
    });
})
