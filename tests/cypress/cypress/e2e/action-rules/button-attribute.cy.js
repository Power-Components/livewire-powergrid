describe('render action rules buttons hide', () => {
    beforeEach(() => {
        cy.visit('/action-rules-button-setattribute');
    });

    it('can render buttons disable', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('contain.html', '<button data-cy="btn-edit-1" class="bg-edit-100">Edit-1</button>')
            .should('contain.html', '<button data-cy="btn-delete-1">Delete-1</button>')

        cy.get('.power-grid-table tbody tr').eq(1)
            .should('contain.html', '<button data-cy="btn-edit-2">Edit-2</button>')
            .should('contain.html', '<button data-cy="btn-delete-2" class="bg-delete-100">Delete-2</button>')
    });
})
