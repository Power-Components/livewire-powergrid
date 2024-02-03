describe('simple', () => {
    beforeEach(() => {
        cy.visit('/filters-number');
    });

    // it('filter between 20 - 25', () => {
    //     cy.get('[wire\\:model="filters.number.price.start"]')
    //         .type('20').should('have.value', '20');
    //
    //     cy.get('[wire\\:model="filters.number.price.end"]')
    //         .type('25').should('have.value', '25');
    //
    //     cy.get('.power-grid-table tbody tr').should('not.contain.text', '26');
    // });
    //
    // it('filter between 15 - 30', () => {
    //     cy.get('[wire\\:model="filters.number.price.start"]')
    //         .type('15').should('have.value', '15');
    //
    //     cy.get('[wire\\:model="filters.number.price.end"]')
    //         .type('30').should('have.value', '30');
    //
    //     cy.get('.power-grid-table tbody tr').should('not.contain.text', '31');
    // });
    //
    // it('filter between 5 - 18', () => {
    //     cy.get('[wire\\:model="filters.number.price.start"]').clear().type('5').should('have.value', '5');
    //
    //     cy.get('[wire\\:model="filters.number.price.end"]')
    //         .type('18').should('have.value', '18');
    //
    //     cy.get('.power-grid-table tbody tr').should('not.contain.text', '19');
    // });

})
