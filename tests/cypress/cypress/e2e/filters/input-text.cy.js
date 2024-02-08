describe('filter input text', () => {
    beforeEach(() => {
        cy.visit('/filters-input-text');
    });

    const inputField = '[data-cy="input_text_default_name"]';
    const optionsField = '[data-cy="input_text_options_default_name"]';
    const tableRows = '.power-grid-table tbody tr';

    const applyFilter = (filterType, filterValue) => {
        cy.get(inputField).clear()
        cy.get(inputField).type(filterValue).should('have.value', filterValue);
        cy.get(optionsField).select(filterType);
    };

    const clearFilter = () => {
        cy.get('[data-cy="enabled-filters"]')
            .should('contain.text', 'Name')

        cy.wait(800) // wait livewire debounce

        cy.get('[data-cy=enabled-filters-clear-name]')
            .click()

        cy.get('[data-cy="enabled-filters"]')
            .should('not.exist')
    }

    it('Filter - "contains" works properly', () => {
        const filterValue = 'Dan';
        applyFilter('contains', filterValue);

        cy.get(tableRows).should('not.have.text', 'Luan');
        cy.url().should('include', `name=${filterValue}`);
    });

    it('Filter - "is" works properly', () => {
        const filterValue = 'John';
        applyFilter('is', filterValue);
    });

    it('Filter - "contains_not" works properly', () => {
        let filterValue = 'John';
        applyFilter('contains_not', filterValue);

        cy.get(tableRows)
            .should('contain.text', 'Luan')
            .should('contain.text', 'Daniel')
            .should('contain.text', 'Claudio')
            .should('contain.text', 'Vitor');

        cy.url().should('include', `name=${filterValue}`);

        clearFilter()

        cy.get(inputField).should('be.empty')

        filterValue = 'Dan';
        applyFilter('contains_not', filterValue);

        cy.get(tableRows)
            .should('not.contains.text', 'Dan')
            .should('contains.text', 'Luan')
            .should('contains.text', 'Claudio')
            .should('contains.text', 'Tio Jobs')
            .should('contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);

        clearFilter()

        cy.get(inputField).should('be.empty')

        filterValue = 'Luan';
        applyFilter('contains_not', filterValue);

        cy.get(tableRows)
            .should('contains.text', 'Dan')
            .should('not.contains.text', 'Luan')
            .should('contains.text', 'Claudio')
            .should('contains.text', 'Tio Jobs')
            .should('contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);

        filterValue = 'an';
        applyFilter('contains_not', filterValue);

        cy.get(tableRows)
            .should('not.contains.text', 'Dan')
            .should('not.contains.text', 'Luan')
            .should('contains.text', 'Claudio')
            .should('contains.text', 'Tio Jobs')
            .should('contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);
    });

    it('Filter - "starts_with" works properly', () => {
        let filterValue = 'Lu';
        applyFilter('starts_with', filterValue);

        cy.get(tableRows)
            .should('not.contains.text', 'Dan')
            .should('contains.text', 'Luan')
            .should('not.contains.text', 'Claudio')
            .should('not.contains.text', 'Tio Jobs')
            .should('not.contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);

        clearFilter()

        filterValue = 'Cl';
        applyFilter('starts_with', filterValue);

        cy.get(tableRows)
            .should('not.contains.text', 'Dan')
            .should('not.contains.text', 'Luan')
            .should('contains.text', 'Claudio')
            .should('not.contains.text', 'Tio Jobs')
            .should('not.contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);

        clearFilter()
    });

    it('Filter - "ends_with" works properly', () => {
        let filterValue = 'obs';
        applyFilter('ends_with', filterValue);

        cy.get(tableRows)
            .should('not.contains.text', 'Dan')
            .should('not.contains.text', 'Luan')
            .should('not.contains.text', 'Claudio')
            .should('contains.text', 'Tio Jobs')
            .should('not.contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);

        clearFilter()

        filterValue = 'an';
        applyFilter('ends_with', filterValue);

        cy.get(tableRows)
            .should('not.contains.text', 'Dan')
            .should('contains.text', 'Luan')
            .should('not.contains.text', 'Claudio')
            .should('not.contains.text', 'Tio Jobs')
            .should('not.contains.text', 'Vitor')

        cy.url().should('include', `name=${filterValue}`);

        clearFilter()
    });
})
