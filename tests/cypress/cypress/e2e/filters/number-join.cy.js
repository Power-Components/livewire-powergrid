describe('filters number - join', () => {
    [
        '/filters-number-join?powerGridTheme=tailwind',
        '/filters-number-join?powerGridTheme=bootstrap'
    ].forEach((route) => {

        /**
         * ['name' => 'Spicy Tofu Stir Fry', 'calories' => 130, 'created_at' => now()],
         * ['name' => 'Quinoa Salad with Avocado', 'calories' => 230, 'created_at' => now()],
         * ['name' => 'Mango Chicken Curry', 'calories' => 330, 'created_at' => now()],
         * ['name' => 'Grilled Salmon with Lemon Dill Sauce', 'calories' => 430, 'created_at' => now()],
         * ['name' => 'Vegetarian Buddha Bowl', 'calories' => 530, 'created_at' => now()],
         * ['name' => 'Pasta Primavera', 'calories' => 630, 'created_at' => now()],
         * ['name' => 'Blueberry Almond Smoothie Bowl', 'calories' => 730, 'created_at' => now()],
         * ['name' => 'Grilled Vegetable Wrap', 'calories' => 830, 'created_at' => now()],
         * ['name' => 'Chocolate Avocado Mousse', 'calories' => 930, 'created_at' => now()],
         * ['name' => 'Caprese Salad', 'calories' => 1030, 'created_at' => now()],
         */

        const tableRows = '.power-grid-table tbody tr';
        const startInput = '[wire\\:model="filters.number.dishes.calories.start"]';
        const endInput = '[wire\\:model="filters.number.dishes.calories.end"]';

        it('filter calories start -> min 400 - ' + route, () => {
            cy.visit(route);

            cy.get(startInput).type('400');
            cy.get(endInput).clear();

            cy.get(tableRows)
                .should('not.contains.text', 'Spicy Tofu Stir Fry')
                .should('not.contains.text', 'Quinoa Salad with Avocado')
                .should('not.contains.text', 'Mango Chicken Curry')

                .should('contains.text', 'Vegetarian Buddha Bowl')
                .should('contains.text', 'Caprese Salad')
        });

        it('filter calories end -> max 400 - ' + route, () => {
            cy.visit(route);
            cy.get(startInput).clear();
            cy.get(endInput).type('400');

            cy.get(tableRows)
                .should('contains.text', 'Spicy Tofu Stir Fry')
                .should('contains.text', 'Quinoa Salad with Avocado')
                .should('contains.text', 'Mango Chicken Curry')

                .should('not.contains.text', 'Grilled Salmon with Lemon Dill Sauce')
                .should('not.contains.text', 'Caprese Salad')
        });

        it('filter calories calories between 200 - 400 - ' + route, () => {
            cy.visit(route);

            cy.get(startInput).type('200');
            cy.get(endInput).type('400');

            cy.get(tableRows)
                .should('contains.text', 'Quinoa Salad with Avocado')
                .should('contains.text', 'Mango Chicken Curry')

                .should('not.contains.text', 'Grilled Salmon with Lemon Dill Sauce')
                .should('not.contains.text', 'Caprese Salad')
        });
    })

})
