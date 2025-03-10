import colors from 'tailwindcss/colors'

export default {
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
