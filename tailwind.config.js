import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // ScanStatus colors
        'border-emerald-800',
        'text-emerald-800',
        'border-green-800',
        'text-green-800',
        'border-orange-800',
        'text-orange-800',
        'border-red-800',
        'text-red-800',
        'border-yellow-800',
        'text-yellow-800',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
