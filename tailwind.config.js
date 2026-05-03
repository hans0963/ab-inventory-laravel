import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/livewire/livewire/resources/views/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/View/Components/**/*.php',
        './app/Livewire/**/*.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                lora: ['Lora', 'serif'],
                inter: ['Inter', 'sans-serif'],
                pacifico: ['Pacifico', 'cursive'],
            },
            colors: {
                terracotta: {
                    DEFAULT: '#E07856',
                    dark: '#C05836',
                    light: '#F09876',
                },
                cream: {
                    DEFAULT: '#FFF8E7',
                    dark: '#F5EED7',
                    light: '#FFFFFF',
                },
                sage: {
                    DEFAULT: '#8B9D83',
                    dark: '#6B7D63',
                    light: '#ABBDB3',
                },
                sienna: {
                    DEFAULT: '#A0522D',
                    dark: '#80320D',
                    light: '#C0724D',
                },
            },
            borderRadius: {
                'lg': '1rem',
                'xl': '1.5rem',
                '2xl': '2rem',
            },
            boxShadow: {
                rustic: '0 4px 6px rgba(160, 82, 45, 0.3)',
                'rustic-lg': '0 10px 15px -3px rgba(160, 82, 45, 0.2), 0 4px 6px -2px rgba(160, 82, 45, 0.1)',
            },
            spacing: {
                sidebar: '18rem',
                card: '1.25rem',
                'section': '2.5rem',
                'container-px': '2rem',
            },
            backgroundImage: {
                grain: "url('/images/grain-texture.png')",
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: 0 },
                    '100%': { opacity: 1 },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: 0 },
                    '100%': { transform: 'translateY(0)', opacity: 1 },
                },
            },
            animation: {
                fadeIn: 'fadeIn 0.5s ease-in-out',
                slideUp: 'slideUp 0.4s ease-out',
            },
        },
    },

    plugins: [forms],
};
