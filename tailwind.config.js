import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
            transitionTimingFunction: {
                emil: 'cubic-bezier(0.32, 0.72, 0, 1)',
            },
            boxShadow: {
                soft: '0 1px 2px rgba(17, 24, 39, 0.04), 0 2px 8px rgba(17, 24, 39, 0.06)',
                'soft-md': '0 1px 2px rgba(17, 24, 39, 0.04), 0 8px 24px rgba(17, 24, 39, 0.08)',
                'soft-lg': '0 2px 4px rgba(17, 24, 39, 0.04), 0 16px 40px rgba(17, 24, 39, 0.10)',
            },
        },
    },

    plugins: [forms],
};
