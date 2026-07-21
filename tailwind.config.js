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
            colors: {
                custom: {
                    light: '#f3f4f6',
                    DEFAULT: '#d1d5db',
                    dark: '#374151',
                },
                'uicm-gray': '#F5F6FA',
                'uicm-green': '#0F4229',
                'uicm-gold': '#D4AF37',
                'uicm-orange': '#EFAD5A',
                'uicm-blue': '#1F5FBF',

                // Pasteles derivados de los 4 colores principales, para badges/etiquetas
                // suaves (fondo claro con carácter + texto legible, no apagado).
                'uicm-green-soft': '#d7ede1',
                'uicm-gold-soft': '#f7e7ab',
                'uicm-gold-soft-text': '#8a6d0a',
                'uicm-orange-soft': '#fbdba8',
                'uicm-orange-soft-text': '#b0530a',
                'uicm-blue-soft': '#cfe0f7',
                'uicm-blue-soft-text': '#164a99',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
