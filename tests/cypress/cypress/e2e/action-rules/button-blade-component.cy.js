describe('render action rules buttons bladeComponent', () => {
    beforeEach(() => {
        cy.visit('/action-rules-button-bladecomponent');
    });

    it('can render buttons bladeComponent', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('contain.html', '["bg-custom-100",1]')
            .should('contain.html', '<button data-cy="btn-delete-1">Delete-1</button>')

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('contain.html', '<button data-cy="btn-edit-2">Edit-2</button>')
            .should('contain.html', '<button data-cy="btn-delete-2">Delete-2</button>')
    });
})
