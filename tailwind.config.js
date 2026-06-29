import defaultTheme from 'tailwindcss/defaultTheme';
import AntdvTheme from "./resources/Theme/antdv-theme.js";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                'sm': '576px',
                'md': '768px',
                'lg': '992px',
                'xl': '1200px',
                '2xl': '1600px',
            },
            colors: {
                primary: AntdvTheme.token.colorPrimary,
                secondary: AntdvTheme.token.colorSecondary,
                "dark-gray": AntdvTheme.token.colorDarkGray,
                "text-primary": AntdvTheme.token.colorText,
            },
        },
    },

    plugins: [],
    darkMode: 'true',
};
