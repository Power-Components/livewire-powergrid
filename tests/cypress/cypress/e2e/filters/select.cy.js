describe('filters number', () => {
    [
        '/filters-select?powerGridTheme=tailwind',
        '/filters-select?powerGridTheme=bootstrap'
    ].forEach((route) => {

        /**
             ['name' => 'Spicy Tofu Stir Fry', 'category_id' => 1],
             ['name' => 'Quinoa Salad with Avocado', 'category_id' => 2],
             ['name' => 'Mango Chicken Curry', 'category_id' => 3],
             ['name' => 'Grilled Salmon with Lemon Dill Sauce', 'category_id' => 1],
             ['name' => 'Vegetarian Buddha Bowl', 'category_id' => 2],
             ['name' => 'Pasta Primavera', 'category_id' => 3],
             ['name' => 'Blueberry Almond Smoothie Bowl', 'category_id' => 1],
             ['name' => 'Grilled Vegetable Wrap', 'category_id' => 2],
             ['name' => 'Chocolate Avocado Mousse', 'category_id' => 3],
             ['name' => 'Caprese Salad', 'category_id' => 1],
         */

        const tableRows = '.power-grid-table tbody tr';
        const select = '[wire\\:model="filters.select.category_id"]';

        it('filter category_id "all" - ' + route, () => {
            cy.visit(route);

            cy.get(select).select(0);

            cy.get(tableRows).should('contains.text', 'Spicy Tofu Stir Fry')
            cy.get(tableRows).should('contains.text', 'Quinoa Salad with Avocado')
            cy.get(tableRows).should('contains.text', 'Mango Chicken Curry')

            cy.get(tableRows).should('contains.text', 'Grilled Salmon with Lemon Dill Sauce')
            cy.get(tableRows).should('contains.text', 'Caprese Salad')
        });

        it('filter category_id "1 - Meat" - ' + route, () => {
            cy.visit(route);

            cy.get(select).select(1);

            cy.get(tableRows).should('contains.text', 'Spicy Tofu Stir Fry')
            cy.get(tableRows).should('contains.text', 'Grilled Salmon with Lemon Dill Sauce')
            cy.get(tableRows).should('contains.text', 'Blueberry Almond Smoothie Bowl')
            cy.get(tableRows).should('contains.text', 'Grilled Salmon with Lemon Dill Sauce')
            cy.get(tableRows).should('contains.text', 'Caprese Salad')

            cy.get(tableRows).should('not.contains.text', 'Quinoa Salad with Avocado')
            cy.get(tableRows).should('not.contains.text', 'Mango Chicken Curry')
            cy.get(tableRows).should('not.contains.text', 'Pasta Primavera')
            cy.get(tableRows).should('not.contains.text', 'Grilled Vegetable Wrap')
            cy.get(tableRows).should('not.contains.text', 'Chocolate Avocado Mousse')
        });


        it('filter category_id "2 - Salad" - ' + route, () => {
            cy.visit(route);

            cy.get(select).select(2);

            cy.get(tableRows).should('contain.text', 'Quinoa Salad with Avocado');
            cy.get(tableRows).should('contain.text', 'Vegetarian Buddha Bowl');
            cy.get(tableRows).should('contain.text', 'Grilled Vegetable Wrap');

            cy.get(tableRows).should('not.contain.text', 'Spicy Tofu Stir Fry');
            cy.get(tableRows).should('not.contain.text', 'Mango Chicken Curry');
            cy.get(tableRows).should('not.contain.text', 'Blueberry Almond Smoothie Bowl');
            cy.get(tableRows).should('not.contain.text', 'Pasta Primavera');
            cy.get(tableRows).should('not.contain.text', 'Grilled Salmon with Lemon Dill Sauce');
            cy.get(tableRows).should('not.contain.text', 'Chocolate Avocado Mousse');
            cy.get(tableRows).should('not.contain.text', 'Caprese Salad');
        });

        it('filter category_id "3 - Garnish" - ' + route, () => {
            cy.visit(route);

            cy.get(select).select(3);

            cy.get(tableRows).should('contain.text', 'Mango Chicken Curry');
            cy.get(tableRows).should('contain.text', 'Pasta Primavera');
            cy.get(tableRows).should('contain.text', 'Chocolate Avocado Mousse');

            cy.get(tableRows).should('not.contain.text', 'Spicy Tofu Stir Fry');
            cy.get(tableRows).should('not.contain.text', 'Quinoa Salad with Avocado');
            cy.get(tableRows).should('not.contain.text', 'Grilled Salmon with Lemon Dill Sauce');
            cy.get(tableRows).should('not.contain.text', 'Blueberry Almond Smoothie Bowl');
            cy.get(tableRows).should('not.contain.text', 'Vegetarian Buddha Bowl');
            cy.get(tableRows).should('not.contain.text', 'Grilled Vegetable Wrap');
            cy.get(tableRows).should('not.contain.text', 'Caprese Salad');
        });
    })
})
