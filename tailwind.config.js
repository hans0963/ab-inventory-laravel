import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',
    
    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                pacifico: ['Pacifico', 'cursive'],
                poppins: ['Poppins', 'sans-serif'],
            },
            colors: {
                terracotta: '#E07856',
                cream: '#FFF8E7',
                sage: '#8B9D83',
                sienna: '#A0522D',
            },
            borderRadius: {
                'lg': '1rem', // softer rounded corners
                'xl': '1.5rem',
            },
            boxShadow: {
                rustic: '0 4px 6px rgba(160, 82, 45, 0.3)', // sienna shadow
            },
        },
    },

    plugins: [forms],
};
