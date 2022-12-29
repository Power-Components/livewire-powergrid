const colors = require('tailwindcss/colors')

module.exports = {
    content: ["./resources/**/*.{html,js}"],
    theme: {
        extend: {
            colors: {
                'pg-primary': colors.slate,
                'pg-secondary': colors.blue,
            }
        }
    }
}
