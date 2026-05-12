import colors from 'tailwindcss/colors'

export default {
    content: ["./resources/**/*.{html,js}"],
    theme: {
        extend: {
            colors: {
                'pg-secondary': colors.blue,
                'accent': colors.green,
            }
        }
    }
}
