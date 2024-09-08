describe('render javascript actions attributes', () => {
    beforeEach(() => {
        cy.visit('/actions-attributes');
    });

    it('can render view button and click for row 1', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('contain.html', 'pgRenderActions({ rowId: 1, parentId: null })')
            .should('contain.html', 'wire:click="$dispatch(\'clickToEdit\'')
            .should('contain.html', 'data-cy="btn-view-1"')
            .should('contain.html', 'class="text-slate-500 items-center flex gap-2 hover:text-slate-700 hover:bg-slate-100 p-1 px-2 rounded"')
            // Eye icon
            .should('contain.html', '<svg class="w-5 text-red-600 !text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>\n' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>\n' +
                '</svg>')
            .should('contain.text', 'View')

        cy.intercept('POST', '/livewire/update', (req) => {
            expect(req.body.components[0].calls[0])
                .to.deep
                .equal({
                    path: "",
                    method: "__dispatch",
                    params: [
                        "clickToEdit",
                        {
                            action: "view",
                            name: "Luan"
                        }
                    ]
                });

        }).as('requestIntercept');

        cy.get('[data-cy=btn-view-1]').click()

        cy.wait('@requestIntercept')
            .its('response.body')
            .should((response) => {
                expect(response.components[0].effects.xjs[0])
                    .to.deep
                    .equal('console.log("Editing #view -  Luan")');
        });
    });

    it('can render "Edit" button and click for row 1', () => {
        cy.get('.power-grid-table tbody tr').eq(0)
            .should('contain.html', 'pgRenderActions({ rowId: 1, parentId: null })')
            .should('contain.html', 'data-cy="btn-edit-1"')
            .should('contain.html', 'class="text-slate-500 items-center flex gap-2 hover:text-slate-700 hover:bg-slate-100 p-1 px-2 rounded"')
            .should('contain.html', 'wire:click="$dispatch(\'clickToEdit\'')
            // Pencil icon
            .should('contain.html', '<svg class="w-5 text-red-600 !text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>\n' +
                '</svg>')
            .should('contain.text', 'Edit')

        cy.intercept('POST', '/livewire/update', (req) => {
            expect(req.body.components[0].calls[0])
                .to.deep
                .equal({
                    path: "",
                    method: "__dispatch",
                    params: [
                        "clickToEdit",
                        {
                            action: "edit",
                            name: "Luan"
                        }
                    ]
                });

        }).as('requestIntercept');

        cy.get('[data-cy=btn-edit-1]').click()

        cy.wait('@requestIntercept')
            .its('response.body')
            .should((response) => {
            expect(response.components[0].effects.xjs[0])
                .to.deep
                .equal('console.log("Editing #edit -  Luan")');
        });
    });

    it('can render view button and click for row 2', () => {
        cy.get('.power-grid-table tbody tr').eq(1)
            .should('contain.html', 'pgRenderActions({ rowId: 2, parentId: null })')
            .should('contain.html', 'wire:click="$dispatch(\'clickToEdit\'')
            // Eye icon
            .should('contain.html', '<svg class="w-5 text-red-600 !text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>\n' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>\n' +
                '</svg>')

        cy.intercept('POST', '/livewire/update', (req) => {
            expect(req.body.components[0].calls[0]).to.deep.equal({
                path: "",
                method: "__dispatch",
                params: [
                    "clickToEdit",
                    {
                        action: "view",
                        name: "Daniel"
                    }
                ]
            });
        }).as('requestIntercept');

        cy.get('[data-cy=btn-view-2').click()

        cy.wait('@requestIntercept')
            .its('response.body')
            .should((response) => {
            expect(response.components[0].effects.xjs[0])
                .to.deep
                .equal('console.log("Editing #view -  Daniel")');
        });
    })

    it('can render "Edit" button and click for row 2', () => {
        cy.get('.power-grid-table tbody tr').eq(1)
            .should('contain.html', 'pgRenderActions({ rowId: 2, parentId: null })')
            .should('contain.html', 'data-cy="btn-edit-2"')
            .should('contain.html', 'class="text-slate-500 items-center flex gap-2 hover:text-slate-700 hover:bg-slate-100 p-1 px-2 rounded"')
            .should('contain.html', 'wire:click="$dispatch(\'clickToEdit\'')
            // Pencil icon
            .should('contain.html', '<svg class="w-5 text-red-600 !text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n' +
                '    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>\n' +
                '</svg>')
            .should('contain.text', 'Edit')

        cy.intercept('POST', '/livewire/update', (req) => {
            expect(req.body.components[0].calls[0]).to.deep.equal({
                path: "",
                method: "__dispatch",
                params: [
                    "clickToEdit",
                    {
                        action: "edit",
                        name: "Daniel"
                    }
                ]
            });

        }).as('requestIntercept');

        cy.get('[data-cy=btn-edit-2]').click()

        cy.wait('@requestIntercept')
            .its('response.body')
            .should((response) => {
            expect(response.components[0].effects.xjs[0])
                .to.deep
                .equal('console.log("Editing #edit -  Daniel")');
        });
    });
})
