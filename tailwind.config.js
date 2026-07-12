import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fff4e6',
                    100: '#ffe4bd',
                    200: '#ffc985',
                    300: '#ffab4d',
                    400: '#ff9f2e',
                    500: '#FF9100',
                    600: '#db7a00',
                    700: '#b56400',
                    800: '#8f4e00',
                    900: '#703d00',
                },
            },
        },
    },

    plugins: [forms, typography],
};
